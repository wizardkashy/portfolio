import discord
from discord.ext import commands
import random
import string
import json
import os
import io
import asyncio
from utils.FFmpegPCMAudio import FFmpegPCMAudio as pcm
from azure.cognitiveservices.speech import AudioDataStream, SpeechConfig, SpeechSynthesizer, SpeechSynthesisOutputFormat
from azure.cognitiveservices.speech.audio import AudioOutputConfig

# from ssml_builder.core import Speech


class audio(commands.Cog): # commands.Cog is IMPORTANT

    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog

    @commands.command()
    async def join(self, ctx):
        # await ctx.send(ctx.message.author)
        # await ctx.send(ctx.message.author.voice)
        # await ctx.send(ctx.message.author.voice.channel)
        channel = ctx.message.author.voice.channel
        
        await channel.connect()
    
    @commands.command()
    async def leave(self, ctx):
        if ctx.author.id == 401585837349011458:
            await ctx.send("'l;leave' :nerd:")
            return
        guild = ctx.message.guild
        voice_client = guild.voice_client
        await voice_client.disconnect()
    
    @commands.command()
    async def say(self, ctx, *, message):
        AZURE_KEY = "nope"
        AZURE_LOCATION = "eastus"
        AZURE_ENDPOINT = "https://eastus.api.cognitive.microsoft.com/sts/v1.0/issuetoken"
        
        # if ctx.author.id == 507357279885066271 or ctx.author.id == 392834812358033409 or ctx.author.id == 590238525841211394 or ctx.author.id == 233763226301497344:
        #     return

        voice_channel = ctx.author.voice.channel
        channel = None
        if voice_channel != None:
            if ctx.author.id == 233763226301497344 or ctx.author.id == 401585837349011458:
                await ctx.send("`\"l;say " + message + "\"` :nerd:")
                return
            # downloading the file
            letters = string.ascii_lowercase
            tmpfilename = "".join(random.choice(letters) for i in range(10))
            tmpfilename += "audio.wav"

            # speech = Speech()
            # speech.say_as(value=f'{ctx.author.nick} says {discord.utils.escape_mentions(message)}', rate='70%', volume='x-soft')

            speech_config = SpeechConfig(subscription=AZURE_KEY, region=AZURE_LOCATION)
            speech_config.set_speech_synthesis_output_format(SpeechSynthesisOutputFormat["Riff48Khz16BitMonoPcm"])
            audio_config = AudioOutputConfig(filename=f"./data/audio-files/{tmpfilename}")
            # synthesizer = SpeechSynthesizer(speech_config=speech_config, audio_config=audio_config)
            synthesizer = SpeechSynthesizer(speech_config=speech_config, audio_config=None)
            
            
            if ctx.message.author.nick != None:
                result = synthesizer.speak_text_async(f"{discord.utils.escape_mentions(message)}").get()
                # print(result.audio_data)
                # print(io.BytesIO(result.audio_data))
                stream = AudioDataStream(result)
                channel = voice_channel.name
                # ctx.voice_client.play(discord.FFmpegPCMAudio(executable="C:/ffmpeg/bin/ffmpeg.exe", source=f"./data/audio-files/{tmpfilename}"))
                ctx.voice_client.play(pcm(io.BytesIO(result.audio_data).read(), pipe=True))
            else:
                result = synthesizer.speak_text_async(f"{discord.utils.escape_mentions(message)}").get()
                # print(result.audio_data)
                # print(io.BytesIO(result.audio_data))
                channel = voice_channel.name
                stream = AudioDataStream(result)
                # ctx.voice_client.play(discord.FFmpegPCMAudio(executable="C:/ffmpeg/bin/ffmpeg.exe", source=f"./data/audio-files/{tmpfilename}"))
                ctx.voice_client.play(pcm(io.BytesIO(result.audio_data).read(), pipe=True))
            
            
            # deleting the file
            # while ctx.voice_client.is_playing() == True:
            #     await asyncio.sleep(0.1)
            #     continue
            # await asyncio.sleep(10)
            # await os.remove(f"./data/audio-files/{tmpfilename}")

        else:
            await ctx.send(str(ctx.author.name) + "is not in a channel.")

#setup function
def setup(client):
    client.add_cog(audio(client))