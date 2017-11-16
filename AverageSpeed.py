import os
import sys
import http.client
import json
import codecs
import time
import datetime
import sqlite3
import subprocess
import platform
    

def getData():
    conn = http.client.HTTPConnection("220.191.224.89")
    conn.request("GET", "/wx/data.php?t=dlzs")
    jsonstr = conn.getresponse()
    return jsonstr

def Main():
    print("绍兴市智慧交通数据分析工具_v1.0内测版\n\n")
    if (getData().status != 200):
        print("远程服务器连接失败，请检查网络状态。")
        exit()
    #print(getData().read())
    data = json.loads(getData().read())
    print("检索到共有" + str(len(data)) + "条路况数据，正在计算。\n")
    reqtime = time.strftime("%Y%m%d%H%M%S",time.localtime(time.time()))
    fullspeed = 0
    for newdata in data:
        #print(newdata["speed"])
        fullspeed = fullspeed + newdata["speed"]
    print("数据分析结束，主城区平均通行速度：" + str(int(fullspeed / len(data))) + " Km/h")
    print("\n\n\nCopyright 2012-2017 DingStudio All Rights Reserved")

def LoopExecute():
    while(1):
        time.sleep(1)
        if (platform.system() == "Windows"):
            os.system('color 0a')
            os.system('cls')
        else:
            os.system('clear')
        Main()

LoopExecute()

