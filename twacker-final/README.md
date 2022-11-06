# twacker

This is my semester final project for my AP Computer Science A class. When running the python file it logs data from applications you use and presents them to you through a web browser.

tracker for computer applications

This application uses the psutil and ctypes libraries in python to get the process name of the foreground window.

Then, it logs it into a json file with a startTime and an endTime, which is then manipulated in front-end javascript to display three charts using the svelte-chartjs library.

These charts include a bar chart, pie chart, and timeline "line" chart. The bar and pie charts are created based on an aggregate of all the time intervals (a sum of the endTime - startTime values for a specific application name) and the timeline chart is created by filtering all the values within a specific range, be it an hour, day, or month.

Separate pages are used to show daily, weekly, monthly, and all-time statistics.

## requirements

### in the base folder:
psutil - https://pypi.org/project/psutil/

ctypes - https://docs.python.org/3/library/ctypes.html

### in the website folder:
svelte - https://www.npmjs.com/package/svelte

svelte-chartjs - https://www.npmjs.com/package/svelte-chartjs

svelte-routing - https://github.com/EmilTholin/svelte-routing

## usage
to track, type `python3 twacker.py` in the base folder
ctrl-c / close terminal to stop tracking

to see interface go into the website folder and type `npm run dev`
then go to localhost:5000
