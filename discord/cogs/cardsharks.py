import discord
from discord.ext import commands
import random
import json

GameState = {}

cards = [[1, "A:clubs:"],
            [2, "2:clubs:"],
            [3, "3:clubs:"],
            [4, "4:clubs:"],
            [5, "5:clubs:"],
            [6, "6:clubs:"],
            [7, "7:clubs:"],
            [8, "8:clubs:"],
            [9, "9:clubs:"],
            [10, "10:clubs:"],
            [11, "J:clubs:"],
            [12, "Q:clubs:"],
            [13, "K:clubs:"],
            [1, "A:diamonds:"],
            [2, "2:diamonds:"],
            [3, "3:diamonds:"],
            [4, "4:diamonds:"],
            [5, "5:diamonds:"],
            [6, "6:diamonds:"],
            [7, "7:diamonds:"],
            [8, "8:diamonds:"],
            [9, "9:diamonds:"],
            [10, "10:diamonds:"],
            [11, "J:diamonds:"],
            [12, "Q:diamonds:"],
            [13, "K:diamonds:"],
            [1, "A:hearts:"],
            [2, "2:hearts:"],
            [3, "3:hearts:"],
            [4, "4:hearts:"],
            [5, "5:hearts:"],
            [6, "6:hearts:"],
            [7, "7:hearts:"],
            [8, "8:hearts:"],
            [9, "9:hearts:"],
            [10, "10:hearts:"],
            [11, "J:hearts:"],
            [12, "Q:hearts:"],
            [13, "K:hearts:"],
            [1, "A:spades:"],
            [2, "2:spades:"],
            [3, "3:spades:"],
            [4, "4:spades:"],
            [5, "5:spades:"],
            [6, "6:spades:"],
            [7, "7:spades:"],
            [8, "8:spades:"],
            [9, "9:spades:"],
            [10, "10:spades:"],
            [11, "J:spades:"],
            [12, "Q:spades:"],
            [13, "K:spades:"]]


class CardSharks(commands.Cog): # commands.Cog is IMPORTANT

    global GameState
    global cards
    
    def __init__(self, client): #passes the client into the cog
        self.client = client # helps us access the client within the cog


    
    

    # GameState = {
    #     "id": {
    #         "turn": 0,
    #         "turncards": [[7, "7:spades:"], [11, "J:hearts"]],
    #         "points": 40
    #     }
    # }





    @commands.command(aliases=['cs', 'cards'])
    async def cardsharks(self, ctx):
        global GameState
        def makeembed(player):
            global GameState
            turncards = GameState[str(player.id)]["turncards"]
            points = GameState[str(player.id)]["points"]
            turn = GameState[str(player.id)]["turn"]
            e = discord.Embed(title = f'Card Sharks | {player.name}#{player.discriminator}', color = discord.Color.from_rgb(random.randint(0, 255), random.randint(0, 255), random.randint(0, 255)))
            for x in range(turn):
                e.add_field(name = f'{turncards[x][1]}', value=f'value: {turncards[x][0]}')
            e.add_field(name="Place your bet on whether the next card will be higher or lower!", value="use l;bet <amount> <higher/lower> to place your bet!", inline=False)
            e.set_footer(text = f'Current Points: {points} | Game: {player.name}#{player.discriminator}', icon_url= player.avatar_url)
            if turn > 6:
                em = discord.Embed(title = f'Card Sharks | Game: {player.name}#{player.discriminator}', color = discord.Color.from_rgb(random.randint(0, 255), random.randint(0, 255), random.randint(0, 255)))
                em.add_field(name = f'Congrats! You got {points} points!')
                em.set_footer(text = "Use `l;cardsharks` to start a new game!")
                
                # resetting values
                GameState.pop(str(player.id))
                # print(GameState)
                return em, e
            return e
        # if not in dictionary, a game hasn't started yet
        if str(ctx.author.id) in GameState.keys():
            await ctx.send("A game is already taking place! do `l;leave` to leave it!")
            return
        await ctx.send("Starting a new game!")
        # initialize a new game by beginning the turncards with a random card (display to user in embed)
        GameState.update({str(ctx.author.id): {"turn": 1, "turncards": [cards[random.randint(0, 51)]], "points": 40}})
        # print(GameState)
        
        e = makeembed(ctx.author)
        await ctx.send(embed=e)
        # # if doesnt respond in a minute then game is over (gameover = true), reset all values. WORK ON LATER
        
        # def check(m):
        #     return (m.content.startswith("l;bet") or m.content.startswith("l;b")) and m.author == ctx.author
        # try:
        #     msg = await self.client.wait_for('message', check=check, timeout=60.0)
        # except asyncio.TimeoutError:
        #     await ctx.send()

        # if all 7 turns are done then set gameOver to true, display total points. could add a leaderboard at this point but that sounds like work and there's a strict cap


    # listen for the player's input, must type in the right style ie higher 100 (alternatively use another command for this)
    @commands.command(aliases=['b'])
    async def bet(self, ctx, amount : int, direction):
        if ctx.author.id == 621929840710516736:
            # sendmessage = await ctx.author.create_dm()
            # await sendmessage.send("suck it")
            return
        def makeembed(player):
            global GameState
            turncards = GameState[str(player.id)]["turncards"]
            points = GameState[str(player.id)]["points"]
            turn = GameState[str(player.id)]["turn"]
            e = discord.Embed(title = f'Card Sharks | {player.name}#{player.discriminator}', color = discord.Color.from_rgb(random.randint(0, 255), random.randint(0, 255), random.randint(0, 255)))
            for x in range(turn):
                e.add_field(name = f'{turncards[x][1]}', value=f'value: {turncards[x][0]}')

            e.set_footer(text = f'Current Points: {points} | Game: {player.name}#{player.discriminator}', icon_url= player.avatar_url)
            if turn > 7 or points == 0:
                em = discord.Embed(title = f'Card Sharks | Game: {player.name}#{player.discriminator}', color = discord.Color.from_rgb(random.randint(0, 255), random.randint(0, 255), random.randint(0, 255)))
                em.add_field(name = f'Congrats! You got {points} points!', value="Use `l;cardsharks` to start a new game!")
                
                # resetting values
                GameState.pop(str(player.id))
                # print(GameState)
                embeds = [e, em]
                return embeds
            e.set_footer(text = f'Current Points: {points} | Game: {player.name}#{player.discriminator}', icon_url= player.avatar_url)
            embeds = [e]
            e.add_field(name="Place your bet on whether the next card will be higher or lower!", value="use l;bet <amount> <higher/lower> to place your bet!", inline=False)
            return embeds
        global GameState
        if str(ctx.author.id) not in GameState.keys():
            await ctx.send("You're not in a game yet! Do `l;cardsharks` to start one!")
            return
        points = GameState[str(ctx.author.id)]["points"]
        turncards = GameState[str(ctx.author.id)]["turncards"]
        turn = GameState[str(ctx.author.id)]["turn"]
        if amount < (int(points / 4)) or amount > points:
            # # if too many or too little (below 1/4) points are bet dont change any values
            # # # display that you have to bet an amount between those values
            await ctx.send(f'Please enter an amount within the valid range ({int(points / 4)} - {points})', delete_after=5)
            return
        else:
            # # when the next card is predicted right, grant the points, else remove
            if direction == "higher" or direction == "h":
                turncards.append(turncards[turn-1])
                while turncards[turn] == turncards[turn-1]:
                    turncards[turn] = cards[random.randint(0, 51)]
                if turncards[turn][0] > turncards[turn-1][0]:
                    await ctx.send(f'Predicted `higher` right! Awarding {amount} points!')
                    points += amount
                elif turncards[turn][0] < turncards[turn-1][0]:
                    await ctx.send(f'Predicted `higher` wrong! Removing {amount} points!')
                    points -= amount
                elif turncards[turn][0] == turncards[turn-1][0]:
                    await ctx.send(f'Equal cards! No points awarded or removed!')
                turn += 1
                GameState.update({str(ctx.author.id): {"turn": turn, "turncards": turncards, "points": points}})
                e = makeembed(ctx.author)
                await ctx.send(embed=e[0])
                try:
                    await ctx.send(embed=e[1])
                except:
                    pass
                # # # display embed with title Card Sharks | author.mention, all the past cards, and current points. Explain that if you just say higher or lower it will bet 1/4 of your points automatically.
            elif direction == "lower" or direction == "l":
                turncards.append(turncards[turn-1])
                while turncards[turn] == turncards[turn-1]:
                    turncards[turn] = cards[random.randint(0, 51)]
                if turncards[turn][0] < turncards[turn-1][0]:
                    await ctx.send(f'Predicted `lower` right! Awarding {amount} points!')
                    points += amount
                elif turncards[turn][0] > turncards[turn-1][0]:
                    await ctx.send(f'Predicted `lower` wrong! Removing {amount} points!')
                    points -= amount
                elif turncards[turn][0] == turncards[turn-1][0]:
                    await ctx.send(f'Equal cards! No points awarded or removed!')
                turn += 1
                GameState.update({str(ctx.author.id): {"turn": turn, "turncards": turncards, "points": points}})
                e = makeembed(ctx.author)
                await ctx.send(embed=e[0])
                try:
                    await ctx.send(embed=e[1])
                except:
                    pass
                
            else:
                await ctx.send("Please enter a valid direction! `higher` (`h`) or `lower` (`l`)", delete_after=5)
        
        # print(GameState)
    @commands.command()
    async def csleave(self, ctx):
        global GameState
        try:
            GameState.pop(str(ctx.author.id))
            await ctx.send("You've left the current game!", delete_after=5)
        except:
            await ctx.send("You're not playing a game!", delete_after=5)

#setup function
def setup(client):
    client.add_cog(CardSharks(client))