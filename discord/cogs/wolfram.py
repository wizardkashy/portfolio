import discord
from discord.ext import commands
import random
import json
import wolframalpha


class wolfram(commands.Cog): # commands.Cog is IMPORTANT

    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog

    @commands.command(aliases=['wa', 'wolfram', 'ask'])
    async def wolframalpha(self, ctx, *, expression):
        appid = ""
        wclient = wolframalpha.Client(appid)
        res = await wclient.query(expression)
        em = discord.Embed(title = "WAAAAAAAAAAAAAAAAA", color = discord.Color.from_rgb(255, 102, 0))
        for pod in res.pods:
            for subpod in pod.subpods:
                print(dir(subpod))
#setup function
def setup(client):
    client.add_cog(wolfram(client))