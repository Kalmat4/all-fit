<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генерация сертификата поверки</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .10);
            width: 100%;
            max-width: 560px;
            padding: 36px 40px 40px;
        }

        .card__title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .card__subtitle {
            font-size: 13px;
            color: #888;
            margin-bottom: 28px;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        label span {
            font-weight: 400;
            color: #aaa;
            font-size: 12px;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #dde1ea;
            border-radius: 8px;
            font-size: 14px;
            color: #222;
            transition: border-color .2s;
            outline: none;
        }

        input:focus {
            border-color: #4f7ef7;
        }

        input.is-invalid {
            border-color: #e53e3e;
        }

        .error {
            font-size: 12px;
            color: #e53e3e;
            margin-top: 4px;
        }

        .hint {
            font-size: 11px;
            color: #aaa;
            margin-top: 4px;
        }

        /* Строка дат */
        .dates-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .date-to-block input {
            background: #f7f8fa;
            color: #888;
            cursor: not-allowed;
        }

        /* Кнопка */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px;
            margin-top: 28px;
            background: #4f7ef7;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .btn:hover {
            background: #3a65d6;
        }

        .btn:active {
            transform: scale(.98);
        }

        .btn:disabled {
            background: #a0b4f0;
            cursor: not-allowed;
        }

        .btn svg {
            flex-shrink: 0;
        }

        /* Успех / ошибка (общее) */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .alert-success {
            background: #f0fff4;
            color: #276749;
            border: 1px solid #9ae6b4;
        }

        .divider {
            height: 1px;
            background: #f0f2f5;
            margin: 24px 0;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="card__title">Сертификат о поверке</div>
        <div class="card__subtitle">Заполните поля — PDF сформируется автоматически</div>

        {{-- Общие ошибки --}}
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="padding-left:16px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('certificate.generate') }}" id="certForm">
            @csrf

            {{-- 1. Номер сертификата --}}
            <div class="field">
                <label for="cert_num">
                    Номер сертификата
                    <span>латинские буквы, цифры, дефис</span>
                </label>
                <input type="text" id="cert_num" name="cert_num" value="{{ old('cert_num', 'VM-07-26-') }}"
                    placeholder="VM-07-26-6206067" class="{{ $errors->has('cert_num') ? 'is-invalid' : '' }}"
                    autocomplete="off">
                @error('cert_num')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 2. Заводской номер --}}
            <div class="field">
                <label for="serial">Заводской номер <span>только цифры</span></label>
                <input type="text" id="serial" name="serial" value="{{ old('serial') }}" placeholder="7525449"
                    class="{{ $errors->has('serial') ? 'is-invalid' : '' }}" autocomplete="off">
                @error('serial')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 3. Год изготовления --}}
            <div class="field">
                <label for="mfg_year">Год изготовления</label>
                <input type="text" id="mfg_year" name="mfg_year" value="{{ old('mfg_year', '2019г.') }}"
                    placeholder="2019г." class="{{ $errors->has('mfg_year') ? 'is-invalid' : '' }}" autocomplete="off">
                @error('mfg_year')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="divider"></div>

            {{-- 4. Пользователь --}}
            <div class="field">
                <label for="user">
                    Пользователь
                    <span>ФИО + адрес без пробела перед городом</span>
                </label>
                <input type="text" id="user" name="user" value="{{ old('user') }}"
                    placeholder="Иванов А. Г.г. Костанай, мкр. Береке д. 67а кв. 22"
                    class="{{ $errors->has('user') ? 'is-invalid' : '' }}" autocomplete="off">
                <div class="hint">Формат как в оригинале: «Фамилия И. О.г. Город, улица д. X кв. X»</div>
                @error('user')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 5. Класс точности --}}
            <div class="field">
                <label for="acc_class">Класс точности</label>
                <input type="text" id="acc_class" name="acc_class" value="{{ old('acc_class', 'В') }}"
                    placeholder="В" class="{{ $errors->has('acc_class') ? 'is-invalid' : '' }}" autocomplete="off"
                    style="max-width: 100px">
                @error('acc_class')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="divider"></div>

            {{-- 6+7. Даты --}}
            <div class="dates-row">
                <div class="field">
                    <label for="date_from">Дата поверки</label>
                    <input type="text" id="date_from" name="date_from" value="{{ old('date_from') }}"
                        placeholder="22.04.2026" maxlength="10"
                        class="{{ $errors->has('date_from') ? 'is-invalid' : '' }}" autocomplete="off">
                    <div class="hint">Формат: ДД.ММ.ГГГГ</div>
                    @error('date_from')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field date-to-block">
                    <label for="date_to_display">Действителен до <span>авто +5 лет</span></label>
                    <input type="text" id="date_to_display" value="" placeholder="автоматически" readonly
                        tabindex="-1">
                </div>
            </div>

            {{-- Кнопка --}}
            <button type="submit" class="btn" id="submitBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Скачать PDF
            </button>
        </form>
    </div>

    <script>
        // Автозаполнение «Действителен до» +5 лет при вводе даты поверки
        document.getElementById('date_from').addEventListener('input', function() {
            const raw = this.value.trim();
            const display = document.getElementById('date_to_display');

            // Маска: автовставка точек при вводе
            let digits = raw.replace(/\D/g, '');
            let masked = '';
            if (digits.length > 0) masked += digits.substring(0, 2);
            if (digits.length > 2) masked += '.' + digits.substring(2, 4);
            if (digits.length > 4) masked += '.' + digits.substring(4, 8);
            if (masked !== raw) this.value = masked;

            // Считаем +5 лет когда введено полностью
            const match = masked.match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
            if (match) {
                const [, dd, mm, yyyy] = match;
                const newYear = parseInt(yyyy) + 5;
                display.value = `${dd}.${mm}.${newYear}`;
            } else {
                display.value = '';
            }
        });

        // Блокируем кнопку на время запроса
        document.getElementById('certForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10">
                <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="0.8s" repeatCount="indefinite"/>
            </circle>
        </svg>
        Генерация…`;
        });
    </script>

</body>

</html>
