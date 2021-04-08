from JSParser import JSParser
from multiprocessing import current_process, Pool
import time
import configparser
config = configparser.ConfigParser(allow_no_value=True)
config.read("settings.ini")

j = JSParser()

def main():
    time_start = time.time()
    j.login("https://www.glovisaa.com/loginProc.do")
    r = j.fetch("https://www.glovisaa.com/memcompany/memAuctionList.do")
    DATE = j.get_auction_date(r)
    MAX_PAGE = j.get_max_page(r)
    if(config['GLOVIS']['AUTOCODE'] == '0'):
        j.rm_csv("car_id3.txt")
        j.create_txt("car_id3.txt")
        j.get_pages(1, MAX_PAGE)
    elif(config['GLOVIS']['AUTOCODE'] == '1'):
        j.rm_csv()
        j.create_csv()
        all_links = j.get_all_links()
        with Pool(20) as p:
            p.map(j.get_car, all_links)
    time_end = time.time() 
    print(time_end - time_start)

if __name__ == '__main__':
    main()