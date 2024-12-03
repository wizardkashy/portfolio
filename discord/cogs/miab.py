import discord
from discord.ext import commands
import random
import json

class miab(commands.Cog): # commands.Cog is IMPORTANT

    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog



    @commands.group(name='miab', invoke_without_command=True)
    async def miab(self, ctx):
        await ctx.send('Message In A Bottle. Subcommands: optin, optout, send')

    

    
    @miab.command(name='optin')
    @commands.cooldown(1, 60, commands.BucketType.user)
    async def optin(self, ctx):
        filename = "./data/miab_opts.json"
        data = ""
        with open(filename, "r") as opts_json:
            data = json.load(opts_json)
            if ctx.author.id in data['ids']:
                await ctx.send("You're already opted in!")
                return
        with open(filename, "w") as opts_json:
            data['ids'].append(ctx.author.id)
            data_json = json.dumps(data)
            opts_json.write(data_json)
            await ctx.send("You're now opted in!")


        # fh = open(filename, "r")
        # lines = fh.readlines()
        # fh = open(filename, "a")
        # for i in range(len(lines)):
        #     lines[i] = lines[i].strip("\n")
        # if str(ctx.author.id) in lines:
        #     await ctx.send("You're already opted in!")
        # else:
        #     print(lines)
        #     fh.write(f'{ctx.author.id}\n')
        #     await ctx.send("You're opted in! do l;miab send <message> to send a message!")
        # fh.close()
        

    @miab.command(name='optout')
    @commands.cooldown(1, 60, commands.BucketType.user)
    async def optout(self, ctx):
        filename = "./data/miab_opts.json"
        with open(filename, "r") as opts_json:
            data = json.load(opts_json)
            if ctx.author.id in data['ids']:
                data['ids'].remove(ctx.author.id)
                await ctx.send("You're now opted out!")
                data_json = json.dumps(data)
                with open(filename, "w") as opts_json_w:
                    opts_json_w.write(data_json)
                return
            await ctx.send("You're already opted out!")
            
        # with open(filename, "r") as fh:
        #     lines = fh.readlines()
        
        # with open(filename, "w") as fh:
        #     for line in lines:
        #         if line.rstrip() != str(ctx.author.id):
        #             f.writelines()
        
        # await ctx.send("You're now opted out!")
        # fh.close()

    @miab.command(name="list")
    async def list(self, ctx):
        ret = ""
        filename = "./data/miab_opts.json"
        with open(filename, "r") as opts_json:
            data = json.load(opts_json)
            for i in data["ids"]:
                user = discord.Client.get_user(int(i))
                ret += f"{user.name}#{user.discriminator}"
        await ctx.send(ret)


    @miab.command(name='send')
    @commands.cooldown(1, 15, commands.BucketType.user)
    async def send(self, ctx, *, message):
        
        filename = "./data/miab_opts.json"
        with open(filename, "r") as opts_json:
            data = json.load(opts_json)
            if ctx.author.id in data['ids']:
                random_id = ctx.author.id
                if len(data['ids']) < 2:
                    await ctx.send("Not enough members have opted in!")
                    return
                for x in range(10):
                    random_id = ctx.author.id
                    i = 0
                    while random_id == ctx.author.id:
                        random_id = data['ids'][random.randint(0, len(data['ids'])-1)]
                        i += 1
                        if i > 10:
                            await ctx.send("Couldn't find a member, either you're really unlucky or there's not enough people opted in.")
                            return
                    member = await self.client.fetch_user(random_id)    
                    try:
                        sendmessage = await member.create_dm()
                        sendembed = discord.Embed(title = "Message In A Bottle", color = discord.Color.from_rgb(random.randint(0, 255), random.randint(0, 255), random.randint(0, 255)))
                        sendembed.add_field(name = f'Sent By: {ctx.author.name}#{ctx.author.discriminator}', value = message, inline = True)
                        sendembed.set_footer(text = "use l;miab optout to stop receiving these messages!", icon_url= self.client.user.avatar_url)
                        sendembed.set_thumbnail(url = ctx.author.avatar_url)
                        await sendmessage.send(embed=sendembed)
                        # await sendmessage.send(f'You have received a message from: **{ctx.author.name}#{ctx.author.discriminator}**\n {message}')
                    except discord.Forbidden:
                        # print("blocked user, remove from db")
                        with open(filename, "w") as opts_edit:
                            data['ids'].remove(member.id)
                            editdata_json = json.dumps(data)
                            opts_edit.write(editdata_json)
                        continue
                    break     
                await ctx.send("Your message has been sent!")
                return
            await ctx.send("You must be opted in to send messages! do `l;miab optin` to opt in!")
        
        @miab.commands
        async def unable_to_send(ctx, error):
            if isinstance(error, discord.HTTPException):
                print("unable to send")
        # content = ""
        # fh = open(filename, "r")
        # lines = fh.readlines()
        # content = fh.read()
        # counter=0
        # for line in lines:
        #     if line != "\n":
        #         counter += 1
        # # print(counter)
        # random_id = ctx.author.id
        # while random_id == ctx.author.id:
        #     random_id = lines[random.randint(0, counter-1)]
        # # print(random_id)
        # member = await self.client.fetch_user(int(random_id))
        # sendmessage = await member.create_dm()
        # await sendmessage.send(f'You have received a message from: **{ctx.author.name}#{ctx.author.discriminator}** \n *{message}*')
        # await ctx.send('Your message has been sent!')

#setup function
def setup(client):
    client.add_cog(miab(client))