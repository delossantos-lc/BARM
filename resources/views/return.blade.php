<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Return Books</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --pink-background: #f8dadd;
            --pink-dark: #d76091;
            --pink-light: #fff2f6;
            --green: #198754;
            --green-hover: #146c43;
            --gray: #737d86;
            --gray-hover: #616a72;
            --dark: #202124;
            --muted: #687079;
            --border: #d9dee3;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--pink-background);
            color: var(--dark);
            font-family: Arial, Helvetica, sans-serif;
        }

        .kiosk-container {
            width: min(92%, 1180px);
            margin: 0 auto;
            padding: 14px 0 35px;
        }

        .kiosk-header {
            padding: 0 15px 15px;
            text-align: center;
        }

        .kiosk-logo {
            display: block;
            width: 75px;
            max-height: 70px;
            margin: 0 auto 7px;
            object-fit: contain;
        }

        .kiosk-title {
            margin: 0;
            color: #242424;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .kiosk-subtitle {
            display: block;
            margin-top: 5px;
            color: #646464;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .kiosk-panel {
            margin-bottom: 18px;
            padding: 24px;
            background: var(--white);
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 18px rgba(115, 75, 84, 0.09);
        }

        .section-title {
            margin-bottom: 4px;
            color: #202020;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .section-description {
            margin-bottom: 18px;
            color: var(--muted);
            font-size: 0.82rem;
        }

        .form-label {
            margin-bottom: 7px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .large-input {
            min-height: 53px;
            padding: 11px 15px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #252525;
            font-size: 0.95rem;
        }

        .large-input:focus {
            background: #ffffff;
            border-color: var(--pink-dark);
            box-shadow: 0 0 0 4px rgba(215, 96, 145, 0.13);
        }

        .readonly-input {
            background-color: #f7f8f9 !important;
            cursor: not-allowed;
        }

        .scan-area {
            padding: 30px 20px;
            text-align: center;
            background: #ffffff;
            border: 2px dashed #ced4da;
            border-radius: 14px;
        }

        .scan-icon {
            margin-bottom: 10px;
            color: #657dd4;
            font-size: 2.5rem;
        }

        .scan-heading {
            margin-bottom: 4px;
            font-size: 1rem;
            font-weight: 800;
        }

        .scan-instruction {
            margin-bottom: 17px;
            color: var(--muted);
            font-size: 0.82rem;
        }

        .rfid-input-wrapper {
            width: min(100%, 600px);
            margin: 0 auto;
        }

        .rfid-status {
            min-height: 24px;
            margin-top: 10px;
            font-size: 0.87rem;
            font-weight: 700;
        }

        .borrower-found {
            padding: 9px 12px;
            background: #e9f8ee;
            border-radius: 9px;
            color: #18763c;
        }

        .borrower-error {
            padding: 9px 12px;
            background: #ffeded;
            border-radius: 9px;
            color: #bd3434;
        }

        .borrowed-books-area {
            min-height: 250px;
        }

        .book-card {
            position: relative;
            margin-bottom: 12px;
            padding: 17px;
            background: #ffffff;
            border: 2px solid #e1e4e8;
            border-radius: 13px;
            cursor: pointer;
            transition: 0.18s ease;
        }

        .book-card:hover {
            border-color: var(--pink-dark);
            box-shadow: 0 5px 13px rgba(0, 0, 0, 0.07);
            transform: translateY(-1px);
        }

        .book-card.selected {
            background: var(--pink-light);
            border-color: var(--pink-dark);
        }

        .book-title {
            margin-bottom: 5px;
            padding-right: 35px;
            color: #252525;
            font-size: 1rem;
            font-weight: 800;
        }

        .book-information {
            margin: 2px 0;
            color: #72777d;
            font-size: 0.82rem;
        }

        .selection-icon {
            color: var(--pink-dark);
            font-size: 1.3rem;
        }

        .empty-return {
            min-height: 230px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px 15px;
            text-align: center;
            color: #8b9298;
            border: 2px dashed #d5d9dd;
            border-radius: 13px;
        }

        .empty-return-icon {
            display: block;
            margin-bottom: 12px;
            color: #59a773;
            font-size: 2.8rem;
        }

        .information-box {
            height: 100%;
            min-height: 76px;
            padding: 13px 15px;
            background: #f5f6f7;
            border-radius: 10px;
        }

        .information-label {
            margin-bottom: 4px;
            color: #666d73;
            font-size: 0.72rem;
        }

        .information-value {
            margin: 0;
            color: #222222;
            font-size: 1rem;
            font-weight: 700;
        }

        .action-panel {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 20px 25px;
        }

        .btn-confirm,
        .btn-cancel {
            width: min(100%, 340px);
            min-height: 52px;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .btn-confirm {
            background: var(--green);
        }

        .btn-confirm:hover,
        .btn-confirm:focus {
            background: var(--green-hover);
            color: #ffffff;
        }

        .btn-confirm:disabled {
            background: #9ebcac;
            cursor: not-allowed;
        }

        .btn-cancel {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background: var(--gray);
            text-decoration: none;
        }

        .btn-cancel:hover,
        .btn-cancel:focus {
            background: var(--gray-hover);
            color: #ffffff;
        }

        .alert {
            border: none;
            border-radius: 12px;
        }

        @media (max-width: 767px) {
            .kiosk-container {
                width: 95%;
            }

            .kiosk-panel {
                padding: 18px;
            }

            .action-panel {
                flex-direction: column;
            }

            .btn-confirm,
            .btn-cancel {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="kiosk-container">

    {{-- HEADER --}}
    <header class="kiosk-header">
        <img
            src="{{ asset('images/lclogo.png') }}"
            class="kiosk-logo"
            alt="Lourdes College Logo"
        >

        <h1 class="kiosk-title">
            Return Books
        </h1>

        <span class="kiosk-subtitle">
            Scan your RFID and select the borrowed books you want to return.
        </span>
    </header>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div
            class="alert alert-success
                   alert-dismissible fade show"
        >
            <i class="fa-solid fa-circle-check me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div
            class="alert alert-danger
                   alert-dismissible fade show"
        >
            <i class="fa-solid fa-circle-exclamation me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div
            class="alert alert-danger
                   alert-dismissible fade show"
        >
            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    <form
        action="{{ route('return.store') }}"
        method="POST"
        id="returnForm"
    >
        @csrf

        {{-- BORROWER INFORMATION --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Borrower Information
            </h2>

            <p class="section-description">
                Scan the RFID card to retrieve the registered borrower
                and their currently borrowed books.
            </p>

            <div class="scan-area">
                <div class="scan-icon">
                    <i class="fa-solid fa-id-card"></i>
                </div>

                <div class="scan-heading">
                    Scan Student or Personnel RFID
                </div>

                <p class="scan-instruction">
                    The borrower information and borrowed books will
                    appear automatically.
                </p>

                <div class="rfid-input-wrapper">
                    <input
                        type="text"
                        name="rfid_tag_uid"
                        id="rfid_tag_uid"
                        class="form-control large-input text-center"
                        placeholder="Scan RFID card"
                        value="{{ old('rfid_tag_uid') }}"
                        autocomplete="off"
                        autofocus
                        required
                    >

                    <div
                        id="rfidStatus"
                        class="rfid-status text-muted"
                    >
                        <i class="fa-solid fa-id-card me-1"></i>
                        Waiting for RFID scan...
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label
                        for="borrower_name"
                        class="form-label"
                    >
                        Borrower Name
                    </label>

                    <input
                        type="text"
                        id="borrower_name"
                        class="form-control large-input readonly-input"
                        placeholder="Name will appear here"
                        readonly
                    >
                </div>

                <div class="col-md-4">
                    <label
                        for="borrower_number"
                        class="form-label"
                    >
                        Student/Employee Number
                    </label>

                    <input
                        type="text"
                        id="borrower_number"
                        class="form-control large-input readonly-input"
                        placeholder="Number will appear here"
                        readonly
                    >
                </div>

                <div class="col-md-4">
                    <label
                        for="borrower_type_display"
                        class="form-label"
                    >
                        Borrower Type
                    </label>

                    <input
                        type="text"
                        id="borrower_type_display"
                        class="form-control large-input readonly-input"
                        placeholder="Student or Personnel"
                        readonly
                    >
                </div>
            </div>

            <input
                type="hidden"
                name="borrower_id"
                id="borrower_id"
            >

            <input
                type="hidden"
                name="borrower_type"
                id="borrower_type"
            >
        </section>

        {{-- BORROWED BOOKS --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Books to Return
            </h2>

            <p class="section-description">
                Select one or more borrowed books before confirming the return.
            </p>

            <div
                id="borrowedBooksArea"
                class="borrowed-books-area"
            >
                <div class="empty-return">
                    <div>
                        <i
                            class="fa-solid fa-books
                                   empty-return-icon"
                        ></i>

                        <h5>
                            Waiting for RFID
                        </h5>

                        <p class="mb-0">
                            Scan a registered RFID card to display
                            currently borrowed books.
                        </p>
                    </div>
                </div>
            </div>

            <div id="selectedBorrowInputs"></div>
        </section>

        {{-- RETURN INFORMATION --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Return Information
            </h2>

            <p class="section-description">
                Review the selected books before confirming the return.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Books Selected
                        </div>

                        <p
                            id="selectedCount"
                            class="information-value"
                        >
                            0
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Overdue Books
                        </div>

                        <p
                            id="overdueCount"
                            class="information-value text-danger"
                        >
                            0
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Return Status
                        </div>

                        <p
                            id="returnStatus"
                            class="information-value"
                        >
                            Waiting for RFID
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ACTION BUTTONS --}}
        <section class="kiosk-panel action-panel">
            <button
                type="submit"
                id="confirmReturnButton"
                class="btn btn-confirm"
                disabled
            >
                <i class="fa-solid fa-check me-2"></i>
                Confirm Return
            </button>

            <a
                href="{{ route('return.exit') }}"
                class="btn-cancel"
            >
                <i class="fa-solid fa-xmark me-2"></i>
                Cancel
            </a>
        </section>
    </form>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const returnForm =
        document.getElementById('returnForm');

    const rfidInput =
        document.getElementById('rfid_tag_uid');

    const rfidStatus =
        document.getElementById('rfidStatus');

    const borrowerNameInput =
        document.getElementById('borrower_name');

    const borrowerNumberInput =
        document.getElementById('borrower_number');

    const borrowerTypeDisplay =
        document.getElementById(
            'borrower_type_display'
        );

    const borrowerIdInput =
        document.getElementById('borrower_id');

    const borrowerTypeInput =
        document.getElementById('borrower_type');

    const borrowedBooksArea =
        document.getElementById(
            'borrowedBooksArea'
        );

    const selectedBorrowInputs =
        document.getElementById(
            'selectedBorrowInputs'
        );

    const selectedCount =
        document.getElementById(
            'selectedCount'
        );

    const overdueCount =
        document.getElementById(
            'overdueCount'
        );

    const returnStatus =
        document.getElementById(
            'returnStatus'
        );

    const confirmReturnButton =
        document.getElementById(
            'confirmReturnButton'
        );

    let scanTimer = null;
    let activeRequest = null;
    let borrowerFound = false;
    let availableBorrows = [];
    const selectedBorrows = new Map();

    function escapeHtml(value) {
        const element =
            document.createElement('div');

        element.textContent =
            value ?? '';

        return element.innerHTML;
    }

    function clearBorrower() {
        borrowerNameInput.value = '';
        borrowerNumberInput.value = '';
        borrowerTypeDisplay.value = '';
        borrowerIdInput.value = '';
        borrowerTypeInput.value = '';

        borrowerFound = false;
        availableBorrows = [];

        selectedBorrows.clear();

        renderBorrowedBooks();
        updateSelection();
    }

    function setRfidStatus(
        message,
        type
    ) {
        const settings = {
            waiting: {
                className:
                    'text-muted',

                icon:
                    'fa-id-card'
            },

            loading: {
                className:
                    'text-primary',

                icon:
                    'fa-spinner fa-spin'
            },

            success: {
                className:
                    'borrower-found',

                icon:
                    'fa-circle-check'
            },

            error: {
                className:
                    'borrower-error',

                icon:
                    'fa-circle-xmark'
            }
        };

        const selected =
            settings[type]
            || settings.waiting;

        rfidStatus.className =
            'rfid-status ' +
            selected.className;

        rfidStatus.innerHTML =
            '<i class="fa-solid ' +
            selected.icon +
            ' me-1"></i>' +
            escapeHtml(message);
    }

    async function findBorrower(rfid) {
        clearBorrower();

        setRfidStatus(
            'Searching the database...',
            'loading'
        );

        returnStatus.textContent =
            'Searching borrower';

        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest =
            new AbortController();

        try {
            const routeTemplate = @json(
                route(
                    'return.borrower.find',
                    ['rfid' => '__RFID__']
                )
            );

            const url =
                routeTemplate.replace(
                    '__RFID__',
                    encodeURIComponent(rfid)
                );

            const response =
                await fetch(url, {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'
                    },

                    signal:
                        activeRequest.signal,

                    cache:
                        'no-store'
                });

            const data =
                await response.json();

            if (
                !response.ok
                ||
                !data.found
            ) {
                throw new Error(
                    data.message
                    ||
                    'RFID card was not found.'
                );
            }

            borrowerIdInput.value =
                data.borrower_id;

            borrowerTypeInput.value =
                data.borrower_type;

            borrowerNameInput.value =
                data.borrower_name;

            borrowerNumberInput.value =
                data.borrower_number;

            borrowerTypeDisplay.value =
                data.borrower_type
                    === 'student'
                    ? 'Student'
                    : 'Personnel';

            borrowerFound = true;

            availableBorrows =
                Array.isArray(
                    data.borrowed_books
                )
                    ? data.borrowed_books
                    : [];

            setRfidStatus(
                'RFID found: ' +
                data.borrower_name,
                'success'
            );

            renderBorrowedBooks();
            updateSelection();

        } catch (error) {
            if (
                error.name
                === 'AbortError'
            ) {
                return;
            }

            clearBorrower();

            setRfidStatus(
                error.message,
                'error'
            );

            returnStatus.textContent =
                'RFID not found';

        } finally {
            activeRequest = null;
        }
    }

    function renderBorrowedBooks() {
        if (!borrowerFound) {
            borrowedBooksArea.innerHTML = `
                <div class="empty-return">
                    <div>
                        <i
                            class="fa-solid fa-books
                                   empty-return-icon"
                        ></i>

                        <h5>
                            Waiting for RFID
                        </h5>

                        <p class="mb-0">
                            Scan a registered RFID card
                            to display borrowed books.
                        </p>
                    </div>
                </div>
            `;

            return;
        }

        if (availableBorrows.length === 0) {
            borrowedBooksArea.innerHTML = `
                <div class="empty-return">
                    <div>
                        <i
                            class="fa-solid fa-circle-check
                                   empty-return-icon"
                        ></i>

                        <h5>
                            No borrowed books
                        </h5>

                        <p class="mb-0">
                            This borrower has no books
                            currently marked as borrowed.
                        </p>
                    </div>
                </div>
            `;

            return;
        }

        let html = '';

        availableBorrows.forEach(
            function (borrow) {
                const id =
                    String(
                        borrow.borrow_id
                    );

                const isSelected =
                    selectedBorrows.has(id);

                const selectedClass =
                    isSelected
                        ? 'selected'
                        : '';

                const checkboxIcon =
                    isSelected
                        ? 'fa-solid fa-square-check'
                        : 'fa-regular fa-square';

                const deadlineClass =
                    borrow.is_overdue
                        ? 'text-danger'
                        : 'text-success';

                const deadlineText =
                    borrow.is_overdue
                        ? 'Overdue'
                        : 'On Time';

                html += `
                    <div
                        class="book-card ${selectedClass}"
                        data-borrow-id="${id}"
                        tabindex="0"
                    >
                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start
                                   gap-3"
                        >
                            <div>
                                <div class="book-title">
                                    ${escapeHtml(
                                        borrow.title
                                    )}
                                </div>

                                <p class="book-information">
                                    Author:
                                    ${escapeHtml(
                                        borrow.author
                                    )}
                                </p>

                                <p class="book-information">
                                    Call Number:
                                    ${escapeHtml(
                                        borrow.call_number
                                    )}
                                </p>

                                <p class="book-information">
                                    Borrowed:
                                    ${escapeHtml(
                                        borrow.borrowed_at
                                    )}
                                </p>

                                <p class="book-information">
                                    Due:
                                    ${escapeHtml(
                                        borrow.due_date
                                    )}
                                </p>

                                <span
                                    class="${deadlineClass}
                                           fw-bold"
                                >
                                    ${deadlineText}
                                </span>
                            </div>

                            <span class="selection-icon">
                                <i
                                    class="${checkboxIcon}"
                                ></i>
                            </span>
                        </div>
                    </div>
                `;
            }
        );

        borrowedBooksArea.innerHTML =
            html;
    }

    function toggleBorrow(borrowID) {
        const record =
            availableBorrows.find(
                function (borrow) {
                    return String(
                        borrow.borrow_id
                    ) === borrowID;
                }
            );

        if (!record) {
            return;
        }

        if (
            selectedBorrows.has(
                borrowID
            )
        ) {
            selectedBorrows.delete(
                borrowID
            );
        } else {
            selectedBorrows.set(
                borrowID,
                record
            );
        }

        renderBorrowedBooks();
        updateSelection();
    }

    function updateSelection() {
        selectedBorrowInputs.innerHTML =
            '';

        let selectedOverdueCount = 0;

        selectedBorrows.forEach(
            function (borrow) {
                const input =
                    document.createElement(
                        'input'
                    );

                input.type = 'hidden';
                input.name = 'borrow_ids[]';
                input.value =
                    borrow.borrow_id;

                selectedBorrowInputs
                    .appendChild(input);

                if (borrow.is_overdue) {
                    selectedOverdueCount++;
                }
            }
        );

        selectedCount.textContent =
            selectedBorrows.size;

        overdueCount.textContent =
            selectedOverdueCount;

        confirmReturnButton.disabled =
            !borrowerFound
            ||
            selectedBorrows.size === 0;

        if (!borrowerFound) {
            returnStatus.textContent =
                'Waiting for RFID';
        } else if (
            availableBorrows.length === 0
        ) {
            returnStatus.textContent =
                'No borrowed books';
        } else if (
            selectedBorrows.size === 0
        ) {
            returnStatus.textContent =
                'Select a book';
        } else {
            returnStatus.textContent =
                'Ready to return';
        }
    }

    rfidInput.addEventListener(
        'input',
        function () {
            clearTimeout(scanTimer);
            clearBorrower();

            const rfid =
                this.value.trim();

            if (!rfid) {
                setRfidStatus(
                    'Waiting for RFID scan...',
                    'waiting'
                );

                return;
            }

            setRfidStatus(
                'Reading RFID...',
                'loading'
            );

            /*
             * The RFID scanner sends no Enter.
             * Search shortly after the last character.
             */
            scanTimer = setTimeout(
                function () {
                    findBorrower(rfid);
                },
                350
            );
        }
    );

    borrowedBooksArea.addEventListener(
        'click',
        function (event) {
            const card =
                event.target.closest(
                    '[data-borrow-id]'
                );

            if (!card) {
                return;
            }

            toggleBorrow(
                card.dataset.borrowId
            );
        }
    );

    borrowedBooksArea.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !== 'Enter'
                &&
                event.key !== ' '
            ) {
                return;
            }

            const card =
                event.target.closest(
                    '[data-borrow-id]'
                );

            if (!card) {
                return;
            }

            event.preventDefault();

            toggleBorrow(
                card.dataset.borrowId
            );
        }
    );

    returnForm.addEventListener(
        'submit',
        function (event) {
            if (!borrowerFound) {
                event.preventDefault();

                alert(
                    'Please scan a registered RFID card.'
                );

                rfidInput.focus();
                rfidInput.select();

                return;
            }

            if (
                selectedBorrows.size === 0
            ) {
                event.preventDefault();

                alert(
                    'Please select at least one book to return.'
                );

                return;
            }

            const confirmed =
                window.confirm(
                    'Confirm the return of the selected book(s)?'
                );

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            confirmReturnButton.disabled =
                true;

            confirmReturnButton.innerHTML =
                '<i class="fa-solid ' +
                'fa-spinner fa-spin me-2"></i>' +
                'Processing Return...';
        }
    );

    clearBorrower();

    setRfidStatus(
        'Waiting for RFID scan...',
        'waiting'
    );

    rfidInput.focus();
});
</script>

</body>
</html>