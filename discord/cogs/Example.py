import discord
from discord.ext import commands

class Example(commands.Cog): # commands.Cog is IMPORTANT

    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog

    @commands.command()
    async def ping(self, ctx): # OHH RIGHT YEAH
        await ctx.send(f'pog: {round(self.client.latency * 1000)}ms')

#setup function
def setup(client):
    client.add_cog(Example(client))