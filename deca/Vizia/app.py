from azure.cognitiveservices.vision.computervision import ComputerVisionClient
from azure.cognitiveservices.vision.computervision.models import VisualFeatureTypes
from msrest.authentication import CognitiveServicesCredentials

from azure.cognitiveservices.speech import AudioDataStream, SpeechConfig, SpeechSynthesizer, SpeechSynthesisOutputFormat
from azure.cognitiveservices.speech.audio import AudioOutputConfig

import simpleaudio as sa

import os
region = "centralus"
key = "AAAAAAAA"

speechregion = "eastus"
speechkey = "AAAAAAAAAA"



# azure CV
credentials = CognitiveServicesCredentials(key)
client = ComputerVisionClient(
    endpoint="https://vizia.cognitiveservices.azure.com/",
    credentials=credentials
)

import time
import cv2
camera = cv2.VideoCapture(0)

def analyze(filename):
    with open(filename, "rb") as f:
        image_analysis = client.analyze_image_in_stream(f,visual_features=[VisualFeatureTypes.objects])

        for obj in image_analysis.objects:

            say = f"{obj.object_property} {getPosition((obj.rectangle.x + obj.rectangle.w / 2), (obj.rectangle.y + obj.rectangle.h / 2))}"
            print(say)
            speech_config = SpeechConfig(subscription=speechkey, region=speechregion)
            speech_config.set_speech_synthesis_output_format(SpeechSynthesisOutputFormat["Riff48Khz16BitMonoPcm"])

            synthesizer = SpeechSynthesizer(speech_config=speech_config, audio_config=None)
            result = synthesizer.speak_text(say)

            playobj = sa.play_buffer(result.audio_data, 1, 2, 48000)
            playobj.wait_done()
            

def getPosition(posX, posY):
    out = ""
    if(posY > 2 * camera.get(cv2.CAP_PROP_FRAME_HEIGHT) / 3):
        out += "bottom "
    elif (posY < camera.get(cv2.CAP_PROP_FRAME_HEIGHT) / 3):
        out += "top "
    else:
        out += "straight "

    if (posX > 2 * camera.get(cv2.CAP_PROP_FRAME_WIDTH) / 3):
        out += "right"
    elif (posX < camera.get(cv2.CAP_PROP_FRAME_WIDTH) / 3):
        out += "left"
    else:
        out += "center"
    return out


# return_value, image = camera.read()
# cv2.imwrite("curImg.png", image)
print("bench")
analyze("bench.png")
time.sleep(3)
print("park bench")
analyze("parkbench.png")
time.sleep(3)
print("couch")
analyze("couch.png")
time.sleep(3)
print("stairs")
analyze("stairs.jpg")
time.sleep(3)
print("bathroom")
analyze("bathroom1.jpg")
time.sleep(3)