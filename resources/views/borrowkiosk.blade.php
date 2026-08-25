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

    <title>Borrow Books</title>

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
        --text-dark: #202124;
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
        padding: 0;
        background: var(--pink-background);
        color: var(--text-dark);
        font-family: Arial, Helvetica, sans-serif;
    }

    .kiosk-container {
        width: min(92%, 1180px);
        margin: 0 auto;
        padding: 14px 0 35px;
    }

    /* HEADER */

    .kiosk-header {
        padding: 0 15px 15px;
        text-align: center;
        background: transparent;
        border: none;
        box-shadow: none;
    }

    .college-logo {
        display: block;
        width: 75px;
        max-height: 70px;
        margin: 0 auto 7px;
        object-fit: contain;
    }

    .college-name {
        margin-bottom: 5px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #424242;
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

    /* PANELS */

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

    /* INPUTS */

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

    /* RFID SCAN BOX */

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

    /* BOOK SEARCH */

    .book-list {
        max-height: 390px;
        overflow-y: auto;
        padding-right: 6px;
    }

    .book-card {
        position: relative;
        margin-bottom: 10px;
        padding: 15px;
        background: #ffffff;
        border: 1px solid #e1e4e8;
        border-radius: 11px;
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
        border: 2px solid var(--pink-dark);
    }

    .book-title {
        margin-bottom: 5px;
        padding-right: 35px;
        color: #252525;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .book-details {
        margin: 1px 0;
        color: #72777d;
        font-size: 0.8rem;
    }

    .selection-icon {
        color: var(--pink-dark);
    }

    /* SELECTED BOOKS */

    .selected-books-box {
        min-height: 220px;
        padding: 16px;
        background: #ffffff;
        border: 2px dashed #d5d9dd;
        border-radius: 13px;
    }

    .selected-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 9px;
        padding: 12px;
        background: var(--pink-light);
        border: 1px solid #edbdcf;
        border-radius: 10px;
    }

    .empty-selection {
        padding: 45px 15px;
        text-align: center;
        color: #8b9298;
    }

    .empty-selection i {
        display: block;
        margin-bottom: 12px;
        color: #59a773;
        font-size: 2.4rem;
    }

    /* INFORMATION BOXES */

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

    /* BUTTONS */

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

    .count-badge {
        padding: 6px 11px;
        background: #5e6871;
        border-radius: 20px;
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 700;
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

        .book-list {
            max-height: 330px;
        }
    }
</style>
</head>

<body>

<div class="kiosk-container">

    {{-- HEADER --}}
    <header class="kiosk-header">
        <div class="college-name">
            Lourdes College Learning Commons
        </div>

        <h1 class="kiosk-title">
            Borrow Books
        </h1>

        <span class="kiosk-subtitle">
            Scan your RFID and select the books you want to borrow.
        </span>
    </header>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa-solid fa-circle-check me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fa-solid fa-circle-exclamation me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
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
        action="{{ route('borrow.store') }}"
        method="POST"
        id="borrowForm"
    >
        @csrf

        {{-- BORROWER INFORMATION --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Borrower Information
            </h2>

            <p class="section-description">
                Scan the RFID card to retrieve the registered borrower.
            </p>

            <div class="scan-area">
                <div class="scan-icon">
                    <i class="fa-solid fa-id-card"></i>
                </div>

                <div class="scan-heading">
                    Scan Student or Personnel RFID
                </div>

                <p class="scan-instruction">
                    The name and information will appear automatically.
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
                value="{{ old('borrower_id') }}"
            >

            <input
                type="hidden"
                name="borrower_type"
                id="borrower_type"
                value="{{ old('borrower_type') }}"
            >
        </section>

        {{-- SELECT BOOKS --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Select Books
            </h2>

            <p class="section-description">
                Search the available books and select the books to borrow.
            </p>

            <input
                type="search"
                id="bookSearch"
                class="form-control large-input mb-3"
                placeholder="Search title, author, or call number"
                autocomplete="off"
            >

            <div class="row g-4">
                <div class="col-lg-7">
                    <div
                        class="d-flex justify-content-between
                               align-items-center mb-3"
                    >
                        <strong>Available Books</strong>

                        <span class="count-badge">
                            {{ $books->count() }} available
                        </span>
                    </div>

                    <div
                        id="bookList"
                        class="book-list"
                    >
                        @forelse($books as $book)
                            <div
                                class="book-card"
                                data-book-id="{{ $book->id }}"
                                data-title="{{ strtolower($book->title ?? '') }}"
                                data-author="{{ strtolower($book->author ?? '') }}"
                                data-call-number="{{ strtolower($book->call_number ?? '') }}"
                                tabindex="0"
                            >
                                <div
                                    class="d-flex justify-content-between
                                           align-items-start gap-3"
                                >
                                    <div>
                                        <div class="book-title">
                                            {{ $book->title }}
                                        </div>

                                        <p class="book-details">
                                            Author:
                                            {{ $book->author ?? 'Unknown' }}
                                        </p>

                                        <p class="book-details">
                                            Call Number:
                                            {{ $book->call_number ?? '-' }}
                                        </p>
                                    </div>

                                    <span class="selection-icon">
                                        <i class="fa-regular fa-square fa-xl"></i>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-selection">
                                <i class="fa-solid fa-books"></i>

                                <strong>
                                    No available books
                                </strong>

                                <p class="mb-0 mt-1">
                                    All books are currently borrowed.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-5">
                    <div
                        class="d-flex justify-content-between
                               align-items-center mb-3"
                    >
                        <strong>Books to Borrow</strong>

                        <span
                            id="selectedCount"
                            class="count-badge"
                        >
                            0 selected
                        </span>
                    </div>

                    <div class="selected-books-box">
                        <div
                            id="emptySelection"
                            class="empty-selection"
                        >
                            <i class="fa-solid fa-books"></i>

                            <strong>No books selected</strong>

                            <p class="mb-0 mt-1">
                                Select a book from the available list.
                            </p>
                        </div>

                        <div id="selectedBooksList"></div>
                    </div>

                    <div id="hiddenBookInputs"></div>
                </div>
            </div>
        </section>

        {{-- BORROW INFORMATION --}}
        <section class="kiosk-panel">
            <h2 class="section-title">
                Borrow Information
            </h2>

            <p class="section-description">
                Review the borrowing details before confirming.
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Books Selected
                        </div>

                        <p
                            id="selectedInformationCount"
                            class="information-value"
                        >
                            0
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Borrowing Period
                        </div>

                        <p class="information-value">
                            7 Days
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="information-box">
                        <div class="information-label">
                            Borrow Status
                        </div>

                        <p
                            id="borrowStatusText"
                            class="information-value"
                        >
                            Waiting for RFID
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ACTIONS --}}
        <section class="kiosk-panel action-panel">
            <button
                type="submit"
                id="borrowButton"
                class="btn btn-confirm"
                disabled
            >
                <i class="fa-solid fa-check me-2"></i>
                Confirm Borrowing
            </button>

            <a
                href="{{ route('borrow.exit') }}"
                class="btn-cancel"
            >
                <i class="fa-solid fa-xmark me-2"></i>
                Cancel
            </a>
        </section>
    </form>
</div>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const borrowForm = document.getElementById('borrowForm');
    const rfidInput = document.getElementById('rfid_tag_uid');
    const rfidStatus = document.getElementById('rfidStatus');

    const borrowerNameInput =
        document.getElementById('borrower_name');

    const borrowerNumberInput =
        document.getElementById('borrower_number');

    const borrowerTypeDisplay =
        document.getElementById('borrower_type_display');

    const borrowerIdInput =
        document.getElementById('borrower_id');

    const borrowerTypeInput =
        document.getElementById('borrower_type');

    const searchInput =
        document.getElementById('bookSearch');

    const bookCards =
        Array.from(document.querySelectorAll('.book-card'));

    const selectedBooksList =
        document.getElementById('selectedBooksList');

    const hiddenBookInputs =
        document.getElementById('hiddenBookInputs');

    const emptySelection =
        document.getElementById('emptySelection');

    const selectedCount =
        document.getElementById('selectedCount');

    const borrowButton =
        document.getElementById('borrowButton');

    const selectedBooks = new Map();

    let borrowerFound = false;
    let scanTimer = null;
    let activeRequest = null;

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function clearBorrower() {
        borrowerNameInput.value = '';
        borrowerNumberInput.value = '';
        borrowerTypeDisplay.value = '';
        borrowerIdInput.value = '';
        borrowerTypeInput.value = '';

        borrowerFound = false;

        updateBorrowButton();
    }

    function setRfidStatus(message, type) {
        const settings = {
            waiting: {
                className: 'text-muted',
                icon: 'fa-id-card'
            },

            loading: {
                className: 'text-primary',
                icon: 'fa-spinner fa-spin'
            },

            success: {
                className: 'borrower-found',
                icon: 'fa-circle-check'
            },

            error: {
                className: 'borrower-error',
                icon: 'fa-circle-xmark'
            }
        };

        const selected =
            settings[type] || settings.waiting;

        rfidStatus.className =
            'rfid-status ' + selected.className;

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

        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();

        try {
            const routeTemplate = @json(
                route(
                    'borrowers.findByRfid',
                    ['rfid' => '__RFID__']
                )
            );

            const url = routeTemplate.replace(
                '__RFID__',
                encodeURIComponent(rfid)
            );

            const response = await fetch(url, {
                method: 'GET',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },

                signal: activeRequest.signal
            });

            const data = await response.json();

            if (!response.ok || !data.found) {
                throw new Error(
                    data.message ||
                    'The RFID card was not found.'
                );
            }

            borrowerNameInput.value =
                data.borrower_name;

            borrowerNumberInput.value =
                data.borrower_number;

            borrowerIdInput.value =
                data.borrower_id;

            borrowerTypeInput.value =
                data.borrower_type;

            borrowerTypeDisplay.value =
                data.borrower_type === 'student'
                    ? 'Student'
                    : 'Personnel';

            borrowerFound = true;

            setRfidStatus(
                'RFID found: ' + data.borrower_name,
                'success'
            );

            updateBorrowButton();
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            clearBorrower();

            setRfidStatus(
                error.message,
                'error'
            );
        } finally {
            activeRequest = null;
        }
    }

    /*
     * RFID scanner sends the number without Enter.
     * Search 350 milliseconds after its final character.
     */
    rfidInput.addEventListener('input', function () {
        clearTimeout(scanTimer);
        clearBorrower();

        const rfid = this.value.trim();

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

        scanTimer = setTimeout(function () {
            findBorrower(rfid);
        }, 350);
    });

    function updateBookSelection() {
        selectedBooksList.innerHTML = '';
        hiddenBookInputs.innerHTML = '';

        selectedBooks.forEach(function (book) {
            const selectedItem =
                document.createElement('div');

            selectedItem.className =
                'selected-item';

            selectedItem.innerHTML = `
                <div>
                    <strong>${escapeHtml(book.title)}</strong>

                    <div class="small text-muted">
                        ${escapeHtml(book.author)}
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    data-remove-book="${book.id}"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            selectedBooksList.appendChild(
                selectedItem
            );

            const hiddenInput =
                document.createElement('input');

            hiddenInput.type = 'hidden';
            hiddenInput.name = 'book_ids[]';
            hiddenInput.value = book.id;

            hiddenBookInputs.appendChild(
                hiddenInput
            );
        });

        emptySelection.style.display =
            selectedBooks.size > 0
                ? 'none'
                : 'block';

        selectedCount.textContent =
            selectedBooks.size +
            (
                selectedBooks.size === 1
                    ? ' selected'
                    : ' selected'
            );

        updateBorrowButton();
    }

    function updateBorrowButton() {
        borrowButton.disabled =
            !borrowerFound ||
            selectedBooks.size === 0;
    }

    function toggleBook(card) {
        const id = card.dataset.bookId;

        if (selectedBooks.has(id)) {
            selectedBooks.delete(id);

            card.classList.remove('selected');

            card.querySelector(
                '.selection-icon'
            ).innerHTML =
                '<i class="fa-regular fa-square fa-xl"></i>';
        } else {
            const titleElement =
                card.querySelector('.book-title');

            const details =
                card.querySelectorAll('.book-details');

            selectedBooks.set(id, {
                id: id,

                title:
                    titleElement.textContent.trim(),

                author:
                    details.length > 0
                        ? details[0]
                            .textContent
                            .replace('Author:', '')
                            .trim()
                        : ''
            });

            card.classList.add('selected');

            card.querySelector(
                '.selection-icon'
            ).innerHTML =
                '<i class="fa-solid fa-square-check fa-xl text-danger"></i>';
        }

        updateBookSelection();
    }

    bookCards.forEach(function (card) {
        card.addEventListener('click', function () {
            toggleBook(card);
        });

        card.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key === 'Enter' ||
                    event.key === ' '
                ) {
                    event.preventDefault();
                    toggleBook(card);
                }
            }
        );
    });

    selectedBooksList.addEventListener(
        'click',
        function (event) {
            const button = event.target.closest(
                '[data-remove-book]'
            );

            if (!button) {
                return;
            }

            const bookId =
                button.dataset.removeBook;

            const card = document.querySelector(
                '.book-card[data-book-id="' +
                bookId +
                '"]'
            );

            if (card) {
                toggleBook(card);
            }
        }
    );

    searchInput.addEventListener('input', function () {
        const query =
            this.value.trim().toLowerCase();

        bookCards.forEach(function (card) {
            const searchText = [
                card.dataset.title,
                card.dataset.author,
                card.dataset.callNumber
            ].join(' ');

            card.style.display =
                searchText.includes(query)
                    ? 'block'
                    : 'none';
        });
    });

    borrowForm.addEventListener('submit', function (event) {
        if (!borrowerFound) {
            event.preventDefault();

            alert(
                'Please scan a registered student or personnel RFID card.'
            );

            rfidInput.focus();
            rfidInput.select();

            return;
        }

        if (selectedBooks.size === 0) {
            event.preventDefault();

            alert(
                'Please select at least one book.'
            );

            return;
        }

        borrowButton.disabled = true;

        borrowButton.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin me-2"></i>' +
            'Processing...';
    });

    updateBookSelection();
    rfidInput.focus();
});
</script>

</body>
</html>