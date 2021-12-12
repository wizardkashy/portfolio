import logging
import threading
import time
import json
from structures.LogEntry import LogEntry

import psutil


import ctypes
from ctypes import wintypes

logentries = None
with open("./website/public/data.json", "r") as fh:
    logentries = json.loads(fh.read())["data"]

# def inputLoop():
#     cmd = ""
#     while (cmd != "exit"):
#         cmd = input(":")
#         if (cmd == "open"):
#             print("Opening html file...")
#         if (cmd == "exit"):
#             break
#     print("out of the loop")

def logLoop():
    sec = 5
    prevpid = 0
    currentEntry = None
    startTime = time.time()
    while(True):
        user32 = ctypes.windll.user32
        h_wnd = user32.GetForegroundWindow()
        pid = wintypes.DWORD()
        user32.GetWindowThreadProcessId(h_wnd, ctypes.byref(pid))
        if prevpid != pid.value:
            
            try:
                currentEntry = psutil.Process(prevpid).name() # replace this with the name later on
            except:
                currentEntry = "Privileged Process"
            print(f"{prevpid}, {currentEntry}")
            logentries.append({"appName": currentEntry, "startTime": startTime, "endTime": time.time()})
            updateJSON()
            startTime = time.time()
            prevpid = pid.value
        time.sleep(sec)

def updateJSON():
    data = None
    with open("./website/public/data.json", "r") as fh:
        data = json.loads(fh.readline())
        data["data"] = logentries
    with open("./website/public/data.json", "w") as fh:
        fh.write(json.dumps(data))

if __name__ == "__main__":
    # inputLoop = threading.Thread(target=inputLoop)
    logLoop = threading.Thread(target=logLoop)
    # inputLoop.start()
    logLoop.start()
