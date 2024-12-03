# LloydBot
Personal discord bot using discord.py's API Wrapper to partake in various shenanigans with my friends
- Text-To-Speech
  - For my online friends without microphones to be able to participate in discussion in voice channels, LloydBot speaks for you!
  - using Azure's Text-To-Speech API and FFMPEG PCM audio output
- Card Sharks card game: see [https://en.wikipedia.org/wiki/Card_Sharks](https://en.wikipedia.org/wiki/Card_Sharks)
  - Independent game states - multiple players can play their own games at the same time.
  - played through discord embeds
- Message in a Bottle
  - opt-in and send a message to a random person!
- Image Manipulation commands
- - Searches for images and gifs online through Pixabay search API and Giphy search API
  - Image Manipulation commands using PIL (formerly Pillow) image library
    - Grab the last image in the text channel and add "meme" text to it
    - Grab the last image in the text channel, assumes it is a Minecraft screenshot and append a "hacked client" GUI to it, making it look like the player in the screenshot is cheating (even though they are not). This is what my friends and I thought was funny when I wrote this.

This bot was developed in 2021 for an older version of discord.py, and likely won't run anymore. The code for interacting with the API's might be outdated too.
