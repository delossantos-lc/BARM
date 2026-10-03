#include <Adafruit_Fingerprint.h>
#include <SoftwareSerial.h>

// Arduino UNO wiring:
// Sensor TX -> Arduino pin 2
// Sensor RX -> Arduino pin 3
SoftwareSerial fingerprintSerial(2, 3);
Adafruit_Fingerprint finger(&fingerprintSerial);

uint16_t lastFingerprintId = 0;
unsigned long lastScanAt = 0;
const unsigned long duplicateDelayMs = 3000;

void setup()
{
  Serial.begin(9600);
  fingerprintSerial.begin(57600);
  delay(100);

  if (!finger.verifyPassword()) {
    Serial.println("SCANNER_ERROR:Sensor not found");
    while (true) {
      delay(1000);
    }
  }

  finger.getTemplateCount();
  Serial.print("SCANNER_READY:");
  Serial.println(finger.templateCount);
}

void loop()
{
  handleComputerCommand();

  int fingerprintId = readFingerprint();

  if (fingerprintId > 0) {
    unsigned long currentTime = millis();

    if (
      fingerprintId != lastFingerprintId ||
      currentTime - lastScanAt >= duplicateDelayMs
    ) {
      Serial.print("FINGERPRINT:");
      Serial.println(fingerprintId);
      lastFingerprintId = fingerprintId;
      lastScanAt = currentTime;
    }

    // Do not scan the same finger repeatedly while it remains on the sensor.
    while (finger.getImage() != FINGERPRINT_NOFINGER) {
      delay(100);
    }
  }

  delay(100);
}

void handleComputerCommand()
{
  if (!Serial.available()) {
    return;
  }

  String command = Serial.readStringUntil('\n');
  command.trim();

  if (command.startsWith("ENROLL:")) {
    uint16_t templateId = command.substring(7).toInt();

    if (templateId < 1 || templateId > 127) {
      Serial.println("ENROLL_ERROR:Template ID must be between 1 and 127");
      return;
    }

    enrollFingerprint(templateId);
    return;
  }

  if (command.startsWith("DELETE:")) {
    uint16_t templateId = command.substring(7).toInt();

    if (templateId < 1 || templateId > 127) {
      Serial.println("DELETE_ERROR:Template ID must be between 1 and 127");
      return;
    }

    uint8_t result = finger.deleteModel(templateId);

    if (result == FINGERPRINT_OK) {
      Serial.print("DELETE_SUCCESS:");
      Serial.println(templateId);
    } else {
      Serial.println("DELETE_ERROR:Unable to delete fingerprint template");
    }
  }
}

bool waitForFingerImage()
{
  while (true) {
    uint8_t result = finger.getImage();

    if (result == FINGERPRINT_OK) {
      return true;
    }

    if (result != FINGERPRINT_NOFINGER) {
      Serial.println("ENROLL_ERROR:Could not capture fingerprint");
      return false;
    }

    delay(100);
  }
}

bool waitForFingerRemoval()
{
  unsigned long startedAt = millis();

  while (finger.getImage() != FINGERPRINT_NOFINGER) {
    if (millis() - startedAt > 15000) {
      Serial.println("ENROLL_ERROR:Finger was not removed");
      return false;
    }

    delay(100);
  }

  return true;
}

void enrollFingerprint(uint16_t templateId)
{
  Serial.print("ENROLL_STARTED:");
  Serial.println(templateId);
  Serial.println("ENROLL_PROMPT:Place finger on the sensor");

  if (!waitForFingerImage()) {
    return;
  }

  if (finger.image2Tz(1) != FINGERPRINT_OK) {
    Serial.println("ENROLL_ERROR:First fingerprint image was unclear");
    return;
  }

  Serial.println("ENROLL_PROMPT:Remove finger");

  if (!waitForFingerRemoval()) {
    return;
  }

  delay(500);
  Serial.println("ENROLL_PROMPT:Place the same finger again");

  if (!waitForFingerImage()) {
    return;
  }

  if (finger.image2Tz(2) != FINGERPRINT_OK) {
    Serial.println("ENROLL_ERROR:Second fingerprint image was unclear");
    return;
  }

  if (finger.createModel() != FINGERPRINT_OK) {
    Serial.println("ENROLL_ERROR:The two fingerprint scans did not match");
    return;
  }

  if (finger.storeModel(templateId) != FINGERPRINT_OK) {
    Serial.println("ENROLL_ERROR:Could not save fingerprint to the sensor");
    return;
  }

  Serial.print("ENROLL_SUCCESS:");
  Serial.println(templateId);
  waitForFingerRemoval();
}

int readFingerprint()
{
  uint8_t result = finger.getImage();

  if (result == FINGERPRINT_NOFINGER) {
    return -1;
  }

  if (result != FINGERPRINT_OK) {
    Serial.println("SCAN_ERROR:Could not capture fingerprint");
    return -1;
  }

  result = finger.image2Tz();

  if (result != FINGERPRINT_OK) {
    Serial.println("SCAN_ERROR:Fingerprint image was unclear");
    return -1;
  }

  result = finger.fingerFastSearch();

  if (result == FINGERPRINT_NOTFOUND) {
    Serial.println("FINGERPRINT_NOT_FOUND");
    return -1;
  }

  if (result != FINGERPRINT_OK) {
    Serial.println("SCAN_ERROR:Fingerprint search failed");
    return -1;
  }

  return finger.fingerID;
}
