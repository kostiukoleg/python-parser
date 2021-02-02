from typing import Any, List, Dict
from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from deep_translator import GoogleTranslator
from multiprocessing import current_process, Pool
from bs4 import BeautifulSoup
from random import choice
from random import uniform
import time
import csv
import requests
import os
import shutil
import re
import fake_useragent

session = requests.Session()

login_link = "https://www.sellcarauction.co.kr/newfront/user/login/user_login_ajax.do"
url = "http://www.sellcarauction.co.kr/newfront/receive/rc/receive_rc_list.do"
car_link = "http://www.sellcarauction.co.kr/newfront/onlineAuc/on/onlineAuc_on_detail.do"

user = fake_useragent.UserAgent().random

headers = { 'user-agent': user, 'accept': '*/*'}

#LOGIN PASSWORD TO SITE sellcarauction.co.kr
data = {
    "i_sUserId": "546801",
    "i_sPswd": "9977"
}                                  

def ko_translate(text, lan):
    try:
        res = GoogleTranslator(source='ko', target=lan).translate(text)
    except Exception as e:
        print('Failed to Translate text. Reason: %s' % e)
        res = ''
    return res

def read_file(file_name, delimiter):
    try:
        return open(file_name).read().split(delimiter)
    except Exception as e:
        print('Can\'t read %s. Reason %s.' % (file_name, e))

proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) }

#session.proxies.update(proxy)
#удаляємо всі зайві пробіли, переноси строк і табуляції
def rm_new_line(string):
        try:
            string = re.sub("[\n\t\r]*", "", string)
            string = re.sub("<!-+(.|\s|\n)*?-+>", "", string)
            string = re.sub("\s{2,}", "", string) 
            string = re.sub("<\s+", "<", string) 
            string = re.sub("\s+>", ">", string) 
            string = re.sub("<\s+\/\s*", "</", string) 
            string = re.sub("&amp;", "&", string) 
            string = re.sub("&quot;", "\"", string) 
            string = re.sub("&apos;", "'", string) 
            string = re.sub("&gt;", "<", string) 
            string = re.sub("&lt;", ">", string) 
            return string
        except Exception as e:
                print('Failed to delete White Space Tabs and New Line. Reason: %s' % e)
#следит чтоб небило длины текста более 5000 символов
def text_len(text, lang):
    new_text = ''
    text = str(list(text)[2])
    if(len(text)>1):
        if(len(text)>5000):
            i = 0
            while (i <= int(len(text)/4999)):
                if(i == int(len(text)/4999)):
                    new_text += rm_new_line(ko_translate(text[i*4999:len(text)], lang))
                else:
                    new_text += rm_new_line(ko_translate(text[i*4999:i+1*4999], lang))
                i+=1
        else: 
            new_text += rm_new_line(ko_translate(text, lang))
        return new_text
#витягуємо IP нашого парсера
def get_ip(html):
    try:
        soup = BeautifulSoup(html, 'html.parser')
        ip = soup.find('span', class_ = 'ip').text.strip()
        ua = soup.find('span', class_ = 'ip').find_next_sibling('span').text.strip()
        return (ip,ua)
    except Exception as e:
        print('Can\'t get MY IP. Reason %s.' % e)
#витягуємо html без сесії
def get_html(url, useragent=None, proxy=None):
    try:
        r = requests.get(url, headers=useragent, proxies=proxy)
        if r.status_code == 200:
            return r.text
        else: return r.status_code
    except Exception as e:
        print('Can\'t get HTML. Reason %s.' % e)
#залогінюємось на сайті
def login(login_link):
    try:
        return session.post(login_link, data=data, headers=headers)
    except Exception as e:
        print('Can\'t get HTML form %s. Reason %s.' % (login_link, e))
#витягуємо html із сесію
def get_shtml(link):
    try:
        html = session.get(link)
        if html.status_code == 200:
            return html.text
        else: return html.status_code
    except Exception as e:
        print('Can\'t get HTML with session. Reason %s.' % e)
#пробег
def get_distance_driven(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.findall("[0-9]+", rm_new_line(soup.find_all("strong", class_="text-right")[5].text.strip()))
        return "".join(res)
    except Exception as e:
        print('Can\'t get driven distance. Reason %s.' % e)
        return ""
#топливо
def get_fuel(html):
    fulel_data = {
        "가솔린":"Бензин",
        "휘발유":"Бензин",
        "경유":"Бензин",
        "디젤":"Дизель",
        "LPG":"LPG",
        "하이브리드":"Гибрид",
        "LPI하이브리드":"LPG гибрид",
        "가솔린하이브리드":"Бензиновый гибрид",
        "디젤하이브리드": "Дизельный гибрид",
        "전기":"Электрокар",
        "가솔린/LPG":"Бензин/LPG"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return fulel_data[rm_new_line(soup.find_all("strong", class_="text-right")[4].text.strip())]
    except Exception as e:
        print('Can\'t get fuel. Reason %s.' % e)
        return ""
#марка авто
def get_car_mark(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:     
        return text_len(soup.select("#body > section.con_top.gray-bg_fin > div.container-fluid.wide_area.mt_1.car_view_check_area > div > div > div > table > tbody > tr:nth-child(7) > td:nth-child(2)")[0].text.strip(),"en")
    except Exception as e:
        print('Can\'t get car mark. Reason %s.' % e)
        return ""
#коробка передач
def get_transmission(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        if(rm_new_line(soup.find_all("strong", class_="text-right")[8].text.strip())=="오토"):
            return "Автомат"
        else:
            return "Механика"
    except Exception as e:
        print('Can\'t get transmission. Reason %s.' % e)
        return ""
#фото авто
def get_img_src(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        items = soup.find_all("img", class_ = "img-fluid")
        images = []
        for item in items:
            if item.has_attr('src'):
                match = re.search('\_S\.[jpeg|jpg|JPG|JPEG|gif|png]+$', item.get('src'))
                if match:
                    images.append(item.get('src').replace('_S', '_L'))
                else: 
                    continue
        return images
    except Exception as e:
        print('Can\'t get img src. Reason %s.' % e)
        return ""
def get_img_str(imgs, link):
    try:
        title = re.sub("\[{1}\d+\]{1}", "",parse(link, get_car_title)) 
        img_str = ''
        for img in imgs :
            str_arr = str(img).split('/')
            str_arr.reverse()
            img_str += str_arr[0]+'[:param:][alt='+title+'][title='+title+']|'
        return img_str[:-1]
    except Exception as e:
        print('Can\'t get Images str. Reason %s' % e)
        return ""
#двигатель
def get_car_displacement(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.findall("[0-9]+", rm_new_line(soup.find_all("strong", class_="text-right")[6].text.strip()))
        return "".join(res)
    except Exception as e:
        print('Can\'t get car displacement. Reason %s.' % e)
        return ""
#название авто
def get_car_title(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return re.sub("\[{1}\d+\]{1}", "", rm_new_line(ko_translate(soup.find("h2", class_="tit_style2").text.strip(), "en")))
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#лот аукциона
def get_lot_id(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.search("\[{1}\d+\]{1}", rm_new_line(ko_translate(soup.find("h2", class_="tit_style2").text.strip(), "en"))).group()
        res = re.sub("\[{1}", "", res)
        res = re.sub("\]{1}", "", res)
        return res
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#год автомобиля
def get_car_year(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.match("[0-9]+", rm_new_line(soup.find_all("strong", class_="text-right")[2].text.strip()))
        if(res):
            return res.group()
    except Exception as e:
        print('Can\'t get car year. Reason %s.' % e)
        return ""
#цвет автомобиля
def get_car_color(html):
    color_data = {
        "흰색":"Белый",
        "은색":"Серебро",
        "검정":"Черный",
        "기타":"Так далее",
        "빨간":"Красный",
        "보라색":"Фиолетовый",
        "주황색":"Оранжевый",
        "초록":"Зеленый",
        "회색":"Серый",
        "금":"Золото",
        "푸른":"Голубой",
        "베이지":"Бежевый"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return color_data[re.sub("\s.+","", rm_new_line(soup.find_all("strong", class_="text-right")[9].text.strip()))]
    except Exception as e:
        print('Can\'t get car color. Reason %s.' % e)
        return ""
#тип автомобиля
def get_car_type(html):
    car_type_data = {
        "승용 (7인승)":"Универсал",
        "승용 (11인승)":"Фургон",
        "승합 (12인승)":"Фура",
        # "Трактор",
        "승용 (5인승)":"Седан",
        # "Родстер",
        # "Пикап",
        # "Мотоцикл",
        "승용 (9인승)":"Минивен",
        "승용 (4인승)":"Хэтчбек",
        # "Кроссовер",
        "승용 (2인승)":"Купе",
        # "Кабриолет",
        # "Багги"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return car_type_data[rm_new_line(soup.find_all("strong", class_="text-right")[10].text.strip())]
    except Exception as e:
        print('Can\'t get car type. Reason %s.' % e)
        return ""
#запись айди авто в файл
def write_car_id(file_name, data):
    try:
        return open(file_name, "a").write(data)
    except Exception as e:
        print('Can\'t wrine file %s. Reason %s.' % (file_name,e))
#цена автомобиля 
def get_car_price(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return rm_new_line(soup.find("strong", class_="i_comm_main_txt2").text.strip())
    except Exception as e:
        print('Can\'t get car price. Reason %s.' % e)
        return ""
#описание авто 
def get_car_description(html):
    description = ''
    soup = BeautifulSoup(html, 'html.parser')
    res = soup.select("#body > section.con_top.gray-bg_fin > div:nth-child(2) > div > div > div > table")[0].string
    if res:
        print(text_len(res, "ru"))
    try:
        description += '<div class="timeline-heading"><h3>Информация об оценке производительности</h3></div>'
        description += text_len(soup.select("#body > section.con_top.gray-bg_fin > div:nth-child(2) > div > div > div > table")[0].contents, "ru")
    except Exception as e:
        print('Can\'t get car description "Performance Evaluation Information". Reason %s.' % e)
    # try:
    #     description += '<div class="timeline-heading"><h3>Информация о вариантах</h3></div>'
    #     description += text_len(str(soup.select("#body > section.con_top.gray-bg_fin > div:nth-child(3) > div > div > table:nth-child(2)")[0]), "ru")
    # except Exception as e:
    #     print('Can\'t get car description "Option information". Reason %s.' % e)
    # try:   
    #     description += text_len(soup.find("ul", class_="comm_list_1").contents, "ru")
    # except Exception as e:
    #     print('Can\'t get car descritpion comm list. Reason %s.' % e)
    # try:
    #     description += text_len(soup.find("div", id="realImg").contents, "ru")
    # except Exception as e:
    #     print('Can\'t get car description image. Reason %s.' % e)
    # try:
    #     description += '<div class="timeline-heading"><h3>Протокол осмотра</h3></div>'
    #     description += text_len(str(soup.select("#body > section.con_top.gray-bg_fin > div.container-fluid.wide_area.mt_1.car_view_check_area > div > div > div > table")[0]), "ru")
    # except Exception as e:
    #     print('Can\'t get car description "Inspection protocol". Reason %s.' % e)
    # return description
#категория для авто
def get_car_category(html):
    try:
        categoty_data = {
            "Genesis":"Genesis",
            "Kia":"Kia Motors",
            "Hyundai":"Hyundai",
            "Ssangyong":"SsangYong",
            "Renault":"Renault",
            "Benz":"Mercedes Benz",
            "Chevrolet":"Chevrolet",
            "Jaguar":"Jaguar",
            "BMW":"BMW",
            "Land":"Land Rover",
            "Peugeot":"Peugeot",
            "Volkswagen":"Volkswagen",
            "Ford":"Ford"
        }
        return categoty_data[re.match("(^\w*)", get_car_title(html)).group(0)]
    except Exception as e:
        print('Can\'t get car category. Reason %s.' % e)
        return ""
def get_car_category_url(html):
    try:
        categoty_url_data = {
            "Genesis":"genesis",
            "Kia Motors":"kia-motors",
            "Hyundai":"hyundai",
            "SsangYong":"ssangyong",
            "Renault":"renault",
            "Mercedes Benz":"mercedes-benz",
            "Chevrolet":"chevrolet",
            "Jaguar":"jaguar",
            "BMW":"bmw",
            "Land Rover":"land-rover",
            "Peugeot":"peugeot",
            "Volkswagen":"volkswagen",
            "Ford":"ford"
        }
        return categoty_url_data[get_car_category(html)]
    except Exception as e:
        print('Can\'t get car category url. Reason %s.' % e)
        return ""
#ID авто для парсингу
def get_car_id(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        items = soup.find_all("div", class_ = "car-title")
        for item in items:
            car_id = re.search('[A-Z]+[0-9]+', item.find("a").get('onclick'))
            if car_id:
                write_car_id("car_id.txt",car_id.group()+"\n")
    except Exception as e:
        print('Can\'t get car id. Reason %s.' % e)
#парсимо сайт із сесії
def parse(link, func):
    login(login_link)
    try:
        html = session.get(link)
        if html.status_code == 200:
            if(func.__name__ == 'get_img_str'):
                return func(get_img_src(html.text), link)
            else:
                return func(html.text)
        else:
            print('Error code %d', html.status_code)
    except Exception as e:
        print('Can\'t get html from site. Reason %s.' % e)
#парсимо всі ссилки на авто по сторінкам
def get_pages(page_start=1, page_end=2):
    for i in range(page_start,page_end+1):
        unf = uniform(1,3)
        time.sleep(unf)
        print("Page %d. Sleep %d." % (i, unf))
        parse(url+"?i_iPageSize=&i_iNowPageNo="+str(i), get_car_id)
    pass
#складаємо всі ссилки на авто в один файл
def get_all_links(car_link):
    return list(map( lambda x: '{}?receivecd={}'.format( car_link, x ), read_file("car_id.txt", "\n")))
#закачуємо усі картинки
def download_images(img_urls):
    try:
        for img_url in img_urls:
            str_arr = str(img_url).split('/')
            str_arr.reverse()
            img_data = requests.get(img_url).content
            with open('./py_img2/' + str_arr[0], 'wb') as handler:
                handler.write(img_data)
    except Exception as e:
        print('Failed download image. Reason: %s' % e)
def rm_csv(name = "data2.csv"):
        try:
            os.remove(name)
            print("File Removed!")
        except Exception as e:
                print('Failed to remove file "data2.csv". Reason: %s' % e)
def create_csv(name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try:
        with open(path, 'w', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow(['Категория', 'URL категории', 'Товар', 'Вариант', 'Описание', 'Цена', 'URL', 'Изображение', 'Артикул', 'Количество', 'Активность', 'Заголовок [SEO]', 'Ключевые слова [SEO]', 'Описание [SEO]', 'Старая цена', 'Рекомендуемый', 'Новый', 'Сортировка', 'Вес', 'Связанные артикулы', 'Смежные категории', 'Ссылка на товар', 'Валюта', 'Свойства'])
    except Exception as e:
            print('Failed to create file "data.csv". Reason: %s' % e)
#записуємо усі данні у файл
def write_csv(data, name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try: 
        with open(path, 'a', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow([data['category'], data['category_url'], data['title'], ' ', data['description'], data['price'], ' ', data['images'], data['article'], data['count'], data['activation'], data['title-seo'], ' ', ' ', ' ', data['recomended'], data['new'], ' ', ' ', ' ', ' ', ' ', data['currency'], data['properties']])
    except Exception as e:
        print('Can\'t write csv file. Reason %s' % e)
#створюємо папку
def create_folder(path):
    try:
        if not os.path.exists(path):
            os.makedirs(path)
    except Exception as e:
        print('Can\'t create folder in path %s. Reason %s' % path, e)
#очищаємо усі файли із папки
def clear_folder(folder):
    for filename in os.listdir(folder):
        file_path = os.path.join(folder, filename)
        try:
            if os.path.isfile(file_path) or os.path.islink(file_path):
                os.unlink(file_path)
            elif os.path.isdir(file_path):
                shutil.rmtree(file_path)
        except Exception as e:
            print('Failed to delete %s. Reason: %s' % (file_path, e))
#витягуємо всі данні на авто
def get_car(link):
    p = current_process()
    print('process counter:', p._identity[0], 'pid:', os.getpid())
    time.sleep(uniform(3,6))
    html = get_shtml(link)
    try:
        if(isinstance(html, int)):
            print(html)
        else:
            car = {}
            color = get_car_color(html)
            car_type = get_car_type(html)
            distance_driven = get_distance_driven(html)
            displacement = get_car_displacement(html)
            year = get_car_year(html)
            transmission = get_transmission(html)
            fuel = get_fuel(html)
            lot_number = get_lot_id(html)
            mark = get_car_mark(html)
            category = get_car_category(html)
            car['category'] = category
            car['category_url'] = get_car_category_url(html)
            car['title'] = get_car_title(html)
            car['title-seo'] = get_car_title(html)
            car['price'] = get_car_price(html)
            car['description'] = get_car_description(html)
            car['images'] = get_img_str(get_img_src(html), link)
            car['count'] = '-1'
            car['activation'] = '1'
            car['currency'] = 'USD'
            car['recomended'] = '0'
            car['new'] = '0'
            car['article'] = get_lot_id(html)
            car['properties'] = 'Цвет=[type=assortmentCheckBox value=%s product_margin=Желтый|Белый|Серебро|Красный|Фиолетовый|Оранжевый|Зеленый|Серый|Золото|Коричневый|Голубой|Черный|Бежевый]&Кузов=[type=assortmentCheckBox value=%s product_margin=Универсал|Фургон|Фура|Трактор|Седан|Родстер|Пикап|Мотоцикл|Минивен|Хэтчбек|Кроссовер|Купе|Кабриолет|Багги]&Пробег=%s&Двигатель=%s&Год=%s&Трансмиссия=[type=assortmentCheckBox value=%s product_margin=Механика|Автомат]&Топливо=[type=assortmentCheckBox value=%s product_margin=Дизель|Бензин|Газ]&Модель=%s&Марка=%s&Номер лота=%s&Аукцион=sellcarauction' % (color, car_type, distance_driven, displacement, year, transmission, fuel, mark, category, lot_number)
            download_images(get_img_src(html))
            write_csv(car)
    except Exception as e:
        print('Can\'t find element. Reason: %s' % e)
        login(login_link)

# for i in range(264): 
#     time.sleep(uniform(3,6))
#     proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) }
#     useragent = {'User-Agent': fake_useragent.UserAgent().random}
    # url = "http://sitespy.ru/my-ip"
    # html = get_html(url, headers, proxy)
    # if(type(html)==int):
    #     print(html)
    # else: 
    #     print(get_ip(html))  
    # print(proxy)

def main():
    time_start = time.time()
    # get_pages(1,86)
    # pass
    rm_csv()
    create_csv()
    create_folder('./py_img2')
    clear_folder('./py_img2/')
    login(login_link)
    my_list = get_all_links(car_link)
    # with Pool(5) as p:
    #     p.map(get_car, my_list) 
    for i in range(0,1):
        time.sleep(uniform(3,6))
        get_car_description(get_shtml(get_all_links(car_link)[i]))
        print("Авто ", i)
    #get_all_links(car_link)
    #get_pages(1, 117)
    time_end = time.time() 
    print(time_end - time_start)
if __name__ == '__main__':
    main()