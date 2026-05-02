#!/usr/bin/env python3
"""
generate_cert.py — генератор сертификата поверки счётчика воды.

Поля (JSON):
  cert_num   - номер сертификата,  напр. "VM-07-26-6206067"
  serial     - заводской номер,    напр. "7525449"
  mfg_year   - год изготовления,   напр. "2019г."
  user       - пользователь+адрес, напр. "Иванов А. Г.г. Костанай, мкр. Береке д. 67а кв. 22"
  acc_class  - класс точности,     напр. "В"
  date_from  - дата поверки,       напр. "22.04.2026"
  template   - путь к шаблону PDF
  output     - путь для результата

date_to считается автоматически: date_from + 5 лет
"""

import sys, json
from io import BytesIO
from datetime import datetime
from pypdf import PdfReader, PdfWriter
from reportlab.pdfgen import canvas
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont

# ── Шрифты ──────────────────────────────────────────────────────────────────
FDIR = "/usr/share/fonts/truetype/liberation"
pdfmetrics.registerFont(TTFont('Reg',  f"{FDIR}/LiberationSerif-Regular.ttf"))
pdfmetrics.registerFont(TTFont('Bold', f"{FDIR}/LiberationSerif-Bold.ttf"))

PAGE_H = 841.9
PAGE_W = 595.3

def ry(top):
    return PAGE_H - top

def cover(c, x0, top, w, h, pad=1):
    """Белый прямоугольник поверх старого текста"""
    c.setFillColorRGB(1, 1, 1)
    c.rect(x0 - pad, ry(top + h + pad), w + pad*2, h + pad*2, fill=1, stroke=0)
    c.setFillColorRGB(0, 0, 0)

def add_5_years(date_str):
    """'22.04.2026' → '22.04.2031'"""
    dt = datetime.strptime(date_str, "%d.%m.%Y")
    return dt.replace(year=dt.year + 5).strftime("%d.%m.%Y")

# ── Страница 1 (русский) ─────────────────────────────────────────────────────
def make_overlay_p1(d):
    buf = BytesIO()
    c = canvas.Canvas(buf, pagesize=(PAGE_W, PAGE_H))

    # 1) Заголовок: «измерений:CERT_NUM»
    #    x0=372.5 top=113.3 bot=125.3
    cover(c, 372, 113.3, 175, 12)
    c.setFont('Bold', 12)
    c.drawString(373, ry(124.5), f"измерений:{d['cert_num']}")

    # 2) Заводской номер
    #    x0=138.0 top=189.3 bot=199.3
    cover(c, 138, 189.3, 110, 11)
    c.setFont('Bold', 10)
    c.drawString(138, ry(199), d['serial'])

    # 3) Год изготовления
    #    x0=508.3 top=230.9 bot=240.9
    cover(c, 507, 230.9, 45, 11)
    c.setFont('Bold', 10)
    c.drawString(508, ry(240.5), d['mfg_year'])

    # 4) Пользователь + адрес
    #    x0=236.0 top=259.1 bot=269.1
    cover(c, 234, 259.1, 310, 11)
    c.setFont('Bold', 10)
    c.drawString(236, ry(269), d['user'])

    # 5) Класс точности
    #    x0=40.0 top=471.9 bot=~481
    cover(c, 38, 471.9, 40, 11)
    c.setFont('Bold', 10)
    c.drawString(40, ry(481.5), d['acc_class'])

    # 6) Дата поверки
    #    x0=121.0 top=505.7 bot=515.7
    cover(c, 119, 505.7, 80, 11)
    c.setFont('Bold', 10)
    c.drawString(121, ry(515.5), d['date_from'])

    # 7) Действителен до (+5 лет автоматически)
    #    x0=345.0 top=505.7 bot=515.7
    cover(c, 343, 505.7, 80, 11)
    c.setFont('Bold', 10)
    c.drawString(345, ry(515.5), d['date_to'])

    c.save()
    buf.seek(0)
    return buf

# ── Страница 2 (казахский) ───────────────────────────────────────────────────
def make_overlay_p2(d):
    buf = BytesIO()
    c = canvas.Canvas(buf, pagesize=(PAGE_W, PAGE_H))

    # 1) Заголовок: «№CERT_NUM»
    #    x0=303.6 top=126.6 bot=138.6
    cover(c, 301, 126.6, 120, 13)
    c.setFont('Bold', 12)
    c.drawString(304, ry(138), f"№{d['cert_num']}")

    # 2) Заводской номер
    #    x0=138.0 top=193.3 bot=203.3
    cover(c, 136, 193.3, 110, 11)
    c.setFont('Bold', 10)
    c.drawString(138, ry(203), d['serial'])

    # 3) Год изготовления
    #    x0=508.3 top=234.9 bot=244.9
    cover(c, 507, 234.9, 45, 11)
    c.setFont('Bold', 10)
    c.drawString(508, ry(244.5), d['mfg_year'])

    # 4) Пользователь + адрес
    #    x0=225.1 top=263.1 bot=273.1
    cover(c, 223, 263.1, 310, 11)
    c.setFont('Bold', 10)
    c.drawString(226, ry(273), d['user'])

    # 5) Класс точности
    #    x0=40.0 top=477.5 bot=~487
    cover(c, 38, 477.5, 40, 11)
    c.setFont('Bold', 10)
    c.drawString(40, ry(487), d['acc_class'])

    # 6) Дата поверки
    #    x0=170.0 top=511.3 bot=521.3
    cover(c, 168, 511.3, 80, 11)
    c.setFont('Bold', 10)
    c.drawString(170, ry(521), d['date_from'])

    # 7) Действителен до (+5 лет автоматически)
    #    x0=401.0 top=511.3 bot=521.3
    cover(c, 399, 511.3, 80, 11)
    c.setFont('Bold', 10)
    c.drawString(401, ry(521), d['date_to'])

    c.save()
    buf.seek(0)
    return buf

# ── Основная функция ─────────────────────────────────────────────────────────
def generate(data: dict, template_path: str, output_path: str):
    # Считаем дату окончания автоматически
    data['date_to'] = add_5_years(data['date_from'])

    reader   = PdfReader(template_path)
    writer   = PdfWriter()
    overlays = [make_overlay_p1(data), make_overlay_p2(data)]

    for i, overlay_buf in enumerate(overlays):
        if i >= len(reader.pages):
            break
        ov_page = PdfReader(overlay_buf).pages[0]
        page    = reader.pages[i]
        page.merge_page(ov_page)
        writer.add_page(page)

    with open(output_path, "wb") as f:
        writer.write(f)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: generate_cert.py '<json>'")
        sys.exit(1)
    data = json.loads(sys.argv[1])
    generate(data, data["template"], data["output"])
    print(data["output"])