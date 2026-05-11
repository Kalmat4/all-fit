<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генерация сертификата поверки</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 16px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            width: 100%;
            max-width: 600px;
            padding: 36px 40px 40px;
        }

        .card__title   { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .card__subtitle{ font-size: 13px; color: #888; margin-bottom: 28px; }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #aaa;
            margin: 24px 0 12px;
        }

        .field { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
        }
        label span { font-weight: 400; color: #aaa; font-size: 12px; }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dde1ea;
            border-radius: 8px;
            font-size: 14px;
            color: #222;
            outline: none;
            transition: border-color .2s;
        }
        input:focus          { border-color: #4f7ef7; }
        input.is-invalid     { border-color: #e53e3e; }
        input[readonly]      { background: #f7f8fa; color: #888; cursor: not-allowed; }

        .error { font-size: 12px; color: #e53e3e; margin-top: 4px; }
        .hint  { font-size: 11px; color: #aaa;    margin-top: 4px; }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

        .divider { height: 1px; background: #f0f2f5; margin: 24px 0 0; }

        /* Кнопки */
        .btn-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }
        .btn:active   { transform: scale(.98); }
        .btn:disabled { opacity: .55; cursor: not-allowed; }

        .btn-word { background: #2b7de9; color: #fff; }
        .btn-word:hover:not(:disabled) { background: #1e65c8; }

        .btn-pdf  { background: #e53e3e; color: #fff; }
        .btn-pdf:hover:not(:disabled)  { background: #c53030; }

        /* Алерты */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error   { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .alert-success { background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; }
        .alert-info    { background: #ebf8ff; color: #2c5282; border: 1px solid #90cdf4; }

        #pdfStub { display: none; }
    </style>
</head>

<body>

<div class="card">
    <div class="card__title">Сертификат о поверке</div>
    <div class="card__subtitle">Заполните поля и выберите формат для скачивания</div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="padding-left:16px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="pdfStub" class="alert alert-info">
        Конвертация в PDF недоступна на Windows. Скачайте файл в формате WORD и конвертируйте самостоятельно.
    </div>

    <form method="POST" id="certForm" action="{{ route('certificate.word') }}">
        @csrf

        {{-- Блок 1: Идентификация счётчика --}}
        <div class="section-label">Данные счётчика</div>

        <div class="row-2">
            <div class="field">
                <label for="cert_number">Номер сертификата</label>
                <input type="text" id="cert_number" name="cert_number"
                    value="{{ old('cert_number', 'VM-07-26-') }}"
                    placeholder="VM-07-26-6206067"
                    class="{{ $errors->has('cert_number') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('cert_number')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="zavod_number">Заводской номер</label>
                <input type="text" id="zavod_number" name="zavod_number"
                    value="{{ old('zavod_number') }}"
                    placeholder="7525449"
                    class="{{ $errors->has('zavod_number') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('zavod_number')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row-3">
            <div class="field">
                <label for="make_year">Год изготовления</label>
                <input type="text" id="make_year" name="make_year"
                    value="{{ old('make_year', '2019г.') }}"
                    placeholder="2019г."
                    class="{{ $errors->has('make_year') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('make_year')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="class">Класс</label>
                <input type="text" id="class" name="class"
                    value="{{ old('class', 'В') }}"
                    placeholder="В"
                    class="{{ $errors->has('class') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('class')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="plomb_number">Номер пломбы</label>
                <input type="text" id="plomb_number" name="plomb_number"
                    value="{{ old('plomb_number') }}"
                    placeholder="12345"
                    class="{{ $errors->has('plomb_number') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('plomb_number')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="field">
            <label for="water_data">Показания счётчика (м³) <span>только число</span></label>
            <input type="number" id="water_data" name="water_data"
                value="{{ old('water_data') }}"
                placeholder="0"
                step="any" min="0"
                style="max-width:180px"
                class="{{ $errors->has('water_data') ? 'is-invalid' : '' }}"
                autocomplete="off">
            @error('water_data')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="divider"></div>

        {{-- Блок 2: Пользователь --}}
        <div class="section-label">Пользователь</div>

        <div class="field">
            <label for="fio_address">ФИО и адрес</label>
            <input type="text" id="fio_address" name="fio_address"
                value="{{ old('fio_address') }}"
                placeholder="Иванов А. Г.г. Костанай, мкр. Береке д. 67а кв. 22"
                class="{{ $errors->has('fio_address') ? 'is-invalid' : '' }}"
                autocomplete="off">
            <div class="hint">Формат: «Фамилия И. О.г. Город, улица д. X кв. X»</div>
            @error('fio_address')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="divider"></div>

        {{-- Блок 3: Даты --}}
        <div class="section-label">Даты поверки</div>

        <div class="row-2">
            <div class="field">
                <label for="check_date">Дата поверки</label>
                <input type="text" id="check_date" name="check_date"
                    value="{{ old('check_date') }}"
                    placeholder="22.04.2026"
                    maxlength="10"
                    class="{{ $errors->has('check_date') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                <div class="hint">Формат: ДД.ММ.ГГГГ</div>
                @error('check_date')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="final_date_display">Действителен до <span>авто +5 лет</span></label>
                <input type="text" id="final_date_display" value="" placeholder="автоматически" readonly tabindex="-1">
            </div>
        </div>

        {{-- Скрытое поле для final_date не нужно — контроллер считает сам --}}

        {{-- Кнопки --}}
        <div class="btn-row">
            <button type="button" class="btn btn-word" id="btnWord" onclick="submitForm('word')">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Скачать WORD
            </button>

            <button type="button" class="btn btn-pdf" id="btnPdf" onclick="submitForm('pdf')">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Скачать PDF
            </button>
        </div>
    </form>
</div>

<script>
    const WORD_URL = '{{ route('certificate.word') }}';
    const PDF_URL  = '{{ route('certificate.pdf') }}';

    // Автоформат и авторасчёт даты
    document.getElementById('check_date').addEventListener('input', function () {
        let digits = this.value.replace(/\D/g, '');
        let masked = '';
        if (digits.length > 0) masked += digits.substring(0, 2);
        if (digits.length > 2) masked += '.' + digits.substring(2, 4);
        if (digits.length > 4) masked += '.' + digits.substring(4, 8);
        if (masked !== this.value) this.value = masked;

        const match = masked.match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
        document.getElementById('final_date_display').value = match
            ? `${match[1]}.${match[2]}.${parseInt(match[3]) + 5}`
            : '';
    });

    function submitForm(type) {
        const form   = document.getElementById('certForm');
        const btnW   = document.getElementById('btnWord');
        const btnP   = document.getElementById('btnPdf');
        const stub   = document.getElementById('pdfStub');

        // Для PDF на Windows — показываем заглушку без отправки
        if (type === 'pdf') {
            const isWindows = navigator.userAgent.includes('Windows');
            if (isWindows) {
                stub.style.display = 'block';
                stub.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        }

        stub.style.display = 'none';
        form.action = type === 'word' ? WORD_URL : PDF_URL;

        btnW.disabled = true;
        btnP.disabled = true;
        btnW.textContent = type === 'word' ? 'Формирование…' : 'Скачать WORD';
        btnP.textContent = type === 'pdf'  ? 'Формирование…' : 'Скачать PDF';

        form.submit();
    }
</script>

</body>
</html>
