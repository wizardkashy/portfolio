import discord # imports the discord module
import random
from discord.ext import commands
import os
import email, imaplib
from email.parser import FeedParser
from bs4 import BeautifulSoup
import datetime
import time
# region credentials
username="nope"
password="nope"
# endregion

mail = imaplib.IMAP4_SSL("imap.gmail.com")


intents = discord.Intents.all()
client = commands.Bot(command_prefix = 'l;', description='wow very cool bot', intents = intents) # wow imagine sev

client.owner_id = 702606919344586913



@client.event
async def on_ready():
    await client.change_presence(status=discord.Status.idle, activity=discord.Game('with potions'))
    print(mail.login(username, password))
    print('bots up')
    


# @client.event
# async def on_command_error(ctx, error):
#     print(error)
#     if isinstance(error, discord.ext.commands.errors.CommandOnCooldown):
#         await ctx.send(error)
#     if isinstance(error, discord.ext.commands.errors.CommandNotFound):
#         pass
#     if isinstance(error, discord.ext.commands.errors.MissingRequiredArgument):
#         pass
    

@client.command(aliases=['8ball', 'eightball'])
async def _8ball(ctx, *, question): # takes multiple arguments as one argument
    responses = [
        "It is certain.",
        "It is decidedly so.",
        "Without a doubt.",
        "Yes - definitely.",
        "You may rely on it.",
        "As I see it, yes.",
        "Most likely.",
        "Outlook good.",
        "Yes.",
        "Signs point to yes.",
        "Reply hazy, try again.",
        "Ask again later.",
        "Better not tell you now.",
        "Cannot predict now.",
        "Concentrate and ask again.",
        "Don't count on it.",
        "My reply is no.",
        "My sources say no.",
        "Outlook not so good.",
        "Very doubtful."]
    await ctx.send(f'Question: `{question}`\nAnswer: {random.choice(responses)}')

def is_twitch_admin(ctx):
    print("Bleh")
    user_ids = [401585837349011458, 609384589601013791, 595016987105689626, 810405026378809354, 309835845849055232, 276492074189062144, 162612070645235712, 437094933011234831, 233293765639405568, 854323970144206859, 233763226301497344, 349390922766614538, 702606919344586913]
    if ctx.author.id not in user_ids:
        print("abc")
        return False
    return True

@client.command(aliases=['code'])
@commands.check(is_twitch_admin)
async def get_code(ctx):
    try:
        mail.select(mailbox="INBOX", readonly=False)
        print('abcabc')
    except:
        mail = imaplib.IMAP4_SSL("imap.gmail.com")
        print(mail.login(username, password))
        print('defdef')
        pass
    mail.select(mailbox="INBOX", readonly=False)
    resp_code, mails = mail.search(None, 'FROM "Twitch" Subject "Your Twitch Login Verification Code"')
    print(resp_code)
    mail_ids = mails[0].decode().split()

    mail_id = mail_ids[-1]
    resp_code, mail_data = mail.fetch(mail_id, '(RFC822)') ## Fetch mail data.
    message = email.message_from_bytes(mail_data[0][1]) ## Construct Message from mail data
    em = discord.Embed(title = f'Twitch Verification Code', color = discord.Color.from_rgb(100, 65, 165))
    f = FeedParser()
    f.feed(message.as_string())
    msg = f.close()
    soup = BeautifulSoup(msg.get_payload(decode=True), 'html.parser')
    the_code = soup.find("div", {"class": "header-message-code"}).text
    date_time_str = message.get("Date")
    date_time_obj = datetime.datetime.strptime(date_time_str, '%a, %d %b %Y %H:%M:%S %z')
    unixtime = date_time_obj.timestamp()
    em.add_field(name="<t:{}:R>".format(int(unixtime)), value="{}".format(the_code))
    em.set_footer(text = f'requested by {ctx.author.name}#{ctx.author.discriminator}', icon_url=ctx.author.avatar_url)
    await ctx.send(embed=em)

    



@client.command(aliases=['clap'])
@commands.has_permissions(kick_members=True)
async def kick(ctx, member : discord.Member, *, reason=None): # the colon reads the member as a member object
    await member.kick(reason = reason)
    await ctx.send("user kicked")


@client.command(aliases=['vamoose'])
@commands.has_permissions(ban_members=True)
async def ban(ctx, member : discord.Member, *, reason=None): 
    await member.ban(reason = reason)
    await ctx.send("user banned")

@client.command(aliases=['comeback'])
@commands.has_permissions(ban_members=True)
async def unban(ctx, *, member):
    banned_users = await ctx.guild.bans()
    member_name, member_discriminator = member.split('#') # what
    for ban_entry in banned_users:
        user = ban_entry.user
        if (user.name, user.discriminator) == (member_name, member_discriminator):
            await ctx.guild.unban(user)
            await ctx.send(f"{member} is now unbanned")

@client.command(aliases=['best-song'])
async def bestsong(ctx):
    await ctx.send("https://soundcloud.com/umru/zombie")

# @client.command()
# async def boop(ctx, member : discord.Member):
#     em = discord.Embed(title = f'{member.name} has been booped by: {ctx.author.name}', color=discord.Color.from_rgb(3, 200, 30))
#     em.add_field(name = "kachow", value="kapow kaching")
#     em.set_footer(text="hi")
#     await ctx.send(embed=em)

@client.command(aliases=['ok'])
@commands.cooldown(1, 5, commands.BucketType.user)
async def ohok(ctx):
    try:
        await ctx.message.delete()
    except:
        pass
    finally:
        await ctx.send("oh")
        await ctx.send("ok")

@client.command()
async def polishcow(ctx):
    await ctx.send("https://medal.tv/clips/40421093/lGAO5MRZOwRn/")




def user_is_me(ctx):
    # print(client.owner_id)
    # print(ctx.author.id)
    # print (ctx.author.id == client.owner_id)
    return ctx.author.id == client.owner_id

@client.command(aliases=['rm', 'remove'])
@commands.check(user_is_me)
async def removemsg(ctx, id : int):
    msg = await ctx.channel.fetch_message(id)
    await msg.delete()

@client.command()
@commands.check(user_is_me)
async def load(ctx, extension):
    client.load_extension(f'cogs.{extension}')
    await ctx.send(f'Loaded cog {extension}!')

@client.command()
@commands.check(user_is_me)
async def unload(ctx, extension):
    client.unload_extension(f'cogs.{extension}')
    await ctx.send(f'Unloaded cog {extension}!')
    

@client.command()
@commands.check(user_is_me)
async def reloadall(ctx):
    for filename in os.listdir('./cogs'):
        if filename.endswith('.py'):
            client.unload_extension(f'cogs.{filename[:-3]}')

    for filename in os.listdir('./cogs'):
        if filename.endswith('.py'):
            client.load_extension(f'cogs.{filename[:-3]}')
    await ctx.send('Reloaded all cogs!')


for filename in os.listdir('./cogs'):
    if filename.endswith('.py'):
        client.load_extension(f'cogs.{filename[:-3]}')

client.run('nope') # thanks for the token m8 /s i am not going to lie i could not care less if you knew my token
