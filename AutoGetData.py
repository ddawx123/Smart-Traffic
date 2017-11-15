import os
import sys
import http.client
import json
import codecs
import time
import datetime

def getData():
    conn = http.client.HTTPConnection("220.191.224.89")
    conn.request("GET", "/wx/data.php?t=dlzs")
    jsonstr = conn.getresponse()
    return jsonstr

if (getData().status != 200):
    print("错误")
    os._exit()
    
print("成功连接服务器，以下为HTTP响应报文：\n" + str(getData().msg) + "\n\n")
print(time.strftime("%Y%m%d%H%M%S",time.localtime(time.time())))
if (input("开始抓取数据请输入[start]：") == "start"):
    #print(getData().read())
    data = json.loads(getData().read())
    print("共有" + str(len(data)) + "条数据。")
    for speed in data:
        print(speed["speed"])
else:
    print("\n用户已取消操作！按任意键退出。");
    input()
    exit()
