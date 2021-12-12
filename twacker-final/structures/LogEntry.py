class LogEntry:
    def __init__(self, app, minutes) -> None:
        self.app = app
        self.minutes = minutes
    def setMinutes(self, newMinutes):
        self.minutes = newMinutes
    def addMinutes(self, minutes):
        self.minutes += minutes
        