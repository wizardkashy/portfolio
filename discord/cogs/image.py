import discord
from discord.ext import commands

import random
import string

from PIL import Image, ImageDraw, ImageFont

import aiohttp
import asyncio
import aiofiles

import os

from pixabay import Image as PixaImg, Video

import json


jiropics = ["https://media.discordapp.net/attachments/828739039333974018/837773329976262717/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837772960479576064/image0.jpg?width=365&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837772406277668914/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837772093894295603/image0.jpg?width=676&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837772030774083635/image0.jpg?width=381&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837771952433135626/image0.jpg?width=381&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837771885588250644/image0.jpg?width=901&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837771862648815656/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837771830789668925/image0.jpg?width=465&height=676",
            "https://media.discordapp.net/attachments/824657262671953971/827723202997256222/image0.png?width=649&height=676",
            "https://media.discordapp.net/attachments/824657262671953971/826991867219673088/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/824657262671953971/826175303628881971/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/824657262671953971/824658078044782612/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/824657262671953971/824657734048678019/image0.jpg?width=801&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837915755298291722/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/837919025195515904/image0.jpg?width=365&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/838089999358230538/image0.jpg?width=365&height=676",
            "https://media.discordapp.net/attachments/828744364980305942/862155544508104714/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828744364980305942/862155178245619732/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/861786560474513418/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/861786118508249088/image2.jpg?width=553&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/861717639953252372/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/861380347589820416/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/860719428438196255/image0.jpg?width=365&height=676",
            "https://media.discordapp.net/attachments/828739039333974018/860718515991478292/image0.jpg?width=728&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/839526747548811284/image0.jpg?width=462&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/839018778066157618/image0.jpg?width=507&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/838821898561388615/unknown.png?width=1041&height=676",
            "https://media.discordapp.net/attachments/828857041304289310/838126476586909750/image0.jpg?width=507&height=676"
            ]
class image(commands.Cog): # commands.Cog is IMPORTANT
    global jiropics
    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog
    
    @commands.command()
    async def meme(self, ctx, memetext1="Top Text", memetext2 = "Bottom Text"):
        # code for finding the most recent image
        image_url = ""
        filename = ""
        letters = string.ascii_lowercase
        tmpfilename = "".join(random.choice(letters) for i in range(10))
        tmpfilename += "image"
        async for message in ctx.channel.history(limit=15):
            if len(message.attachments) != 0:
                if message.attachments[0].content_type != None:
                    if message.attachments[0].content_type.startswith("image/"):
                        # await ctx.send(f'message {message.id} has an image: {message.jump_url}\n{message.attachments[0].url}')
                        image_url = message.attachments[0].proxy_url
                        # print(image_url)
                        filename = image_url.split("/")[-1]
                        tmpfilename += "." + filename.split(".")[-1]
                        # print(tmpfilename)
                        # print(filename)
                        break
        
        if image_url == "":
            await ctx.send(f'the image is not within 15 messages')
        # code for downloading said most recent image
        async with aiohttp.ClientSession() as session:
            async with session.get(image_url) as response:
                if response.status == 200:
                    fh = await aiofiles.open(f'./tmpimages/{tmpfilename}', mode='wb')
                    await fh.write(await response.read())
                    await fh.close()
        
        # opening the most recent image

        img = Image.open(f'./tmpimages/{tmpfilename}')
        d = ImageDraw.Draw(img)

        # parsing the meme text
        # applying the meme text to the image and downloading it

        fontsize = int(img.height / 8)
        strokewidth = int(img.height / 120)
        # print(fontsize)

        pos1 = [int(img.width/2), int(img.height/20)]
        pos2 = [int(img.width/2), int(int(img.height * 19) / 20)]
        # print(pos1)
        # print(pos2)

        memefont = ImageFont.truetype("./data/fonts/impact.ttf", fontsize)
        d.text((int(pos1[0]),int(pos1[1])), memetext1.upper(), fill="white", anchor="mt", font=memefont, align='center', stroke_width=strokewidth, stroke_fill="black")
        d.text((int(pos2[0]),int(pos2[1])), memetext2.upper(), fill="white", anchor="mb", font=memefont, align='center', stroke_width=strokewidth, stroke_fill="black")
        # d.multiline_text((int(pos1[0]),int(pos1[1])), memetext1.upper(), fill="white", font=memefont, align='center', stroke_width=strokewidth, stroke_fill="black")
        # d.multiline_text((int(pos2[0]),int(pos2[1])), memetext2.upper(), fill="white", font=memefont, align='center', stroke_width=strokewidth, stroke_fill="black")

        # # sending the memeified image to the channel

        img.save(f'./tmpimages/{tmpfilename}')
        await ctx.send(file=discord.File(f'./tmpimages/{tmpfilename}'))

        # deleting both images

        os.remove(f'./tmpimages/{tmpfilename}')


    @commands.command(aliases=['vape'])
    async def hacks(self, ctx):
        # code for finding the most recent image
        image_url = ""
        filename = ""
        letters = string.ascii_lowercase
        tmpfilename = "".join(random.choice(letters) for i in range(10))
        tmpfilename += "image"
        async for message in ctx.channel.history(limit=15):
            if len(message.attachments) != 0:
                if message.author.id != 831537008366321706:
                    if message.attachments[0].content_type != None:
                        if message.attachments[0].content_type.startswith("image/"):
                            # await ctx.send(f'message {message.id} has an image: {message.jump_url}\n{message.attachments[0].url}')
                            image_url = message.attachments[0].proxy_url
                            # print(image_url)
                            filename = image_url.split("/")[-1]
                            tmpfilename += "." + filename.split(".")[-1]
                            # print(tmpfilename)
                            # print(filename)
                            break
        
        if image_url == "":
            await ctx.send(f'the image is not within 15 messages')
        # code for downloading said most recent image
        async with aiohttp.ClientSession() as session:
            async with session.get(image_url) as response:
                if response.status == 200:
                    fh = await aiofiles.open(f'./tmpimages/{tmpfilename}', mode='wb')
                    await fh.write(await response.read())
                    await fh.close()
        
        # opening the most recent image

        img = Image.open(f'./tmpimages/{tmpfilename}')
        vapegui = Image.open(f'./tmpimages/vapegui37924.png')
        # print(f'{vapegui.width}\n{vapegui.height}')
        baseheight = int((img.height / 4))
        # print(baseheight)
        hpercent = (baseheight/float(vapegui.height))
        # print(hpercent)
        wsize = int((float(vapegui.width)*float(hpercent)))
        # print(wsize)
        vapegui = vapegui.resize((wsize, baseheight))
        # print(f'{vapegui.width}\n{vapegui.height}')
        img.paste(vapegui, box=(int(img.width - vapegui.width), int(0)), mask=vapegui)


        img.save(f'./tmpimages/{tmpfilename}')
        await ctx.send(file=discord.File(f'./tmpimages/{tmpfilename}'))

        # deleting both images

        os.remove(f'./tmpimages/{tmpfilename}')

    @commands.command(aliases=['av', 'avy'])
    async def avatar(self, ctx, user : discord.Member = None):
        if user == None:
            user = ctx.author
        
        await ctx.send(user.avatar_url)

    @commands.command(aliases=['img', 'otter', 'jdqn', "otte"])
    async def imgsearch(self, ctx, *, searchexpression = "otter"):
        API_KEY = "nope"
        image = PixaImg(API_KEY)

        ims = image.search(q=searchexpression, lang="es", image_type="photo", safesearch="true")
        # print(ims)
        try:
            imgindex = random.randint(0, len(ims['hits']))
            await ctx.send(ims['hits'][imgindex]['largeImageURL'])
        except:
            await ctx.send("no results D:", delete_after=3)

    @commands.command(aliases=['gif'])
    async def giphysearch(self, ctx, *, searchexpression = "otter"):
        API_KEY = "nope"
        session = aiohttp.ClientSession()

        searchexpression.replace(" ", "+")

        query = "http://api.giphy.com/v1/gifs/search?api_key=" + API_KEY + "&q=" + searchexpression
        resp = await session.get(query)
        data = json.loads(await resp.text())
        gifindex = random.randint(0, len(data["data"]))  
        await ctx.send(data["data"][gifindex]["images"]["original"]["url"])
        await session.close()

    # @commands.command(aliases=['shiba'])
    # async def jiro(self, ctx):
    #     global jiropics
    #     await ctx.send(jiropics[random.randint(0, len(jiropics) - 1)])


#setup function
def setup(client):
    client.add_cog(image(client))