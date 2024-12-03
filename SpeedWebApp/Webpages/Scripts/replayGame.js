$(document).ready(() => {
// import {postJSON} from "./httprequests.js";
let freezeAll = false;
let freezeHost = false;
let freezeGuest = false;
let defaultDeck = [
    { symbol: "A♠️", value: 1, color: "black" },
    { symbol: "2♠️", value: 2, color: "black" },
    { symbol: "3♠️", value: 3, color: "black" },
    { symbol: "4♠️", value: 4, color: "black" },
    { symbol: "5♠️", value: 5, color: "black" },
    { symbol: "6♠️", value: 6, color: "black" },
    { symbol: "7♠️", value: 7, color: "black" },
    { symbol: "8♠️", value: 8, color: "black" },
    { symbol: "9♠️", value: 9, color: "black" },
    { symbol: "10♠️", value: 10, color: "black" },
    { symbol: "J♠️", value: 11, color: "black" },
    { symbol: "Q♠️", value: 12, color: "black" },
    { symbol: "K♠️", value: 13, color: "black" },

    { symbol: "A♦️", value: 1, color: "red" },
    { symbol: "2♦️", value: 2, color: "red" },
    { symbol: "3♦️", value: 3, color: "red" },
    { symbol: "4♦️", value: 4, color: "red" },
    { symbol: "5♦️", value: 5, color: "red" },
    { symbol: "6♦️", value: 6, color: "red" },
    { symbol: "7♦️", value: 7, color: "red" },
    { symbol: "8♦️", value: 8, color: "red" },
    { symbol: "9♦️", value: 9, color: "red" },
    { symbol: "10♦️", value: 10, color: "red" },
    { symbol: "J♦️", value: 11, color: "red" },
    { symbol: "Q♦️", value: 12, color: "red" },
    { symbol: "K♦️", value: 13, color: "red" },

    { symbol: "A♣️", value: 1, color: "black" },
    { symbol: "2♣️", value: 2, color: "black" },
    { symbol: "3♣️", value: 3, color: "black" },
    { symbol: "4♣️", value: 4, color: "black" },
    { symbol: "5♣️", value: 5, color: "black" },
    { symbol: "6♣️", value: 6, color: "black" },
    { symbol: "7♣️", value: 7, color: "black" },
    { symbol: "8♣️", value: 8, color: "black" },
    { symbol: "9♣️", value: 9, color: "black" },
    { symbol: "10♣️", value: 10, color: "black" },
    { symbol: "J♣️", value: 11, color: "black" },
    { symbol: "Q♣️", value: 12, color: "black" },
    { symbol: "K♣️", value: 13, color: "black" },

    { symbol: "A♥️", value: 1, color: "red" },
    { symbol: "2♥️", value: 2, color: "red" },
    { symbol: "3♥️", value: 3, color: "red" },
    { symbol: "4♥️", value: 4, color: "red" },
    { symbol: "5♥️", value: 5, color: "red" },
    { symbol: "6♥️", value: 6, color: "red" },
    { symbol: "7♥️", value: 7, color: "red" },
    { symbol: "8♥️", value: 8, color: "red" },
    { symbol: "9♥️", value: 9, color: "red" },
    { symbol: "10♥️", value: 10, color: "red" },
    { symbol: "J♥️", value: 11, color: "red" },
    { symbol: "Q♥️", value: 12, color: "red" },
    { symbol: "K♥️", value: 13, color: "red" },
];
let currentLeftCardIdx = null;
let currentRightCardIdx = null;
let hostCardsLeft = 20;
let guestCardsLeft = 20;
let hostStuns = 0;
let guestStuns = 0;
function banner(countDown, message) {
    // create a banner for the beginning and end screens.
    $("body").prepend(
        "<div class='banner'> <p>" +
        message +
        "</p> <p class='timer'>" +
        countDown +
        "</p></div>"
    );
    bannertimer = window.setInterval(() => {
        if (countDown == 1) {
            window.clearInterval(bannertimer);
            $(".banner").remove();
        }
        countDown--;
        $(".banner p.timer").text(countDown);
    }, 1000);
}
let matchEvents = JSON.parse(document.getElementById("matchEvents").innerHTML);
document.getElementById("matchEvents").remove();
// console.log(matchEvents);

let initDeckOrder = JSON.parse(document.getElementById("initDeckOrder").innerHTML);
document.getElementById("initDeckOrder").remove();
// console.log(initDeckOrder);
// let curDeck = initDeckOrder;

let matchID = document.getElementById("replayID").innerHTML;

let curDeck = JSON.parse(JSON.stringify(initDeckOrder));

function tiebreak() {
    // this function is called at the beginning of the game and at the point where none of the cards are valid.
    leftCards = curDeck.filter(
        (card) => card["location"] == "leftTiebreakerPile"
    );
    // if there are no more cards in the tiebreaker pile, return false;
    if (leftCards.length == 0) {
        return false;
    }

    // if cards can be placed.. first set the status "used" to the cards that the new ones will be placed on.
    // for instance, if the left card is a KHearts and it's about to be placed on by a tiebreaker card, we set KHearts to used since it doesn't matter in the game anymore.
    if (currentLeftCardIdx != null && currentRightCardIdx != null) {
        // place a banner for tiebreak cards only if it's not the first cards being placed.
        freezeAll = true;
        banner(5, "placing a tiebreak card..."); // we will resume gameplay when the cards are in the right position. in 5 seconds, we set freezeAll to false and refresh the playfield.
        curDeck[currentLeftCardIdx]["location"] = "used";
        curDeck[currentRightCardIdx]["location"] = "used";
    }

    // sort the cards by the lower order. if card1<card2 in order, -1 favors card1, so it gets placed first. 1 favors card2
    leftCards.sort((card1, card2) =>
        card1["order"] < card2["order"] ? -1 : 1
    );
    leftCardIdx = curDeck.findIndex((card) => card == leftCards[0]);
    // get the card's index in the array to edit its location in place.

    // replace the top card of the shown pile with the tiebreak card.
    currentLeftCardIdx = leftCardIdx;
    curDeck[leftCardIdx]["location"] = "leftPile";

    // same logic for the right card
    rightCards = curDeck.filter(
        (card) => card["location"] == "rightTiebreakerPile"
    );
    rightCards.sort((card1, card2) =>
        card1["order"] < card2["order"] ? -1 : 1
    );
    rightCardIdx = curDeck.findIndex((card) => card == rightCards[0]);
    currentRightCardIdx = rightCardIdx;
    curDeck[rightCardIdx]["location"] = "rightPile";
    window.setTimeout(() => {
        freezeAll = false;
        refreshPlayfield();
    }, 5000);
    return true;
}
function refreshPlaceCard(host_guest, cardLoc, domLoc) {
    let card = curDeck.filter((card) => card["location"] == cardLoc);
    if (card.length != 0) {
        card = card[0];
        $("#dealField " + host_guest + " " + domLoc).html(
            "<p>" + card["card"]["symbol"] + "</p>"
        );
    } else {
        $("#dealField " + host_guest + " " + domLoc).html("<p>N/A</p>");
    }
}
function refreshPlayfield() {
    // for now, place the number of cards in the left and right tiebreaker piles into the cards themselves.
    // get the number of cards currently in the right and left tiebreaker piles and use jquery to append.
    let leftTiebreakerPileCount = curDeck.filter(
        (card) => card["location"] == "leftTiebreakerPile"
    ).length;
    let rightTiebreakerPileCount = curDeck.filter(
        (card) => card["location"] == "rightTiebreakerPile"
    ).length;
    // attach their number to the designated piles.
    $("#placeField #leftTiebreakerPile").html(
        "<p>" + leftTiebreakerPileCount + " cards left.</p>"
    );
    $("#placeField #rightTiebreakerPile").html(
        "<p>" + rightTiebreakerPileCount + " cards left.</p>"
    );

    // console.log(currentLeftCardIdx + " " + currentRightCardIdx);
    // use currentLeftCardIdx and currentRightCardIdx to set the leftPile and rightPile's html
    $("#placeField #leftPlayPile").html(
        "<p>" + curDeck[currentLeftCardIdx]["card"]["symbol"] + "</p>"
    );
    $("#placeField #rightPlayPile").html(
        "<p>" + curDeck[currentRightCardIdx]["card"]["symbol"] + "</p>"
    );

    // place the host and guest's cards in the right places.

    // host pile
    refreshPlaceCard("#hostCards", "hostCard1", ".card1");
    refreshPlaceCard("#hostCards", "hostCard2", ".card2");
    refreshPlaceCard("#hostCards", "hostCard3", ".card3");
    refreshPlaceCard("#hostCards", "hostCard4", ".card4");
    refreshPlaceCard("#hostCards", "hostCard5", ".card5");
    // guest pile
    refreshPlaceCard("#guestCards", "guestCard1", ".card1");
    refreshPlaceCard("#guestCards", "guestCard2", ".card2");
    refreshPlaceCard("#guestCards", "guestCard3", ".card3");
    refreshPlaceCard("#guestCards", "guestCard4", ".card4");
    refreshPlaceCard("#guestCards", "guestCard5", ".card5");

    // fill out the information row in the bottom.
    hostCardsLeft = curDeck.filter(
        (card) =>
            card["location"] == "hostDeck" ||
            card["location"].startsWith("hostCard")
    );
    guestCardsLeft = curDeck.filter(
        (card) =>
            card["location"] == "guestDeck" ||
            card["location"].startsWith("guestCard")
    );
    $("#hostCardsLeft").text("" + hostCardsLeft.length + " cards left.");
    $("#guestCardsLeft").text("" + guestCardsLeft.length + " cards left.");

    $("#hostStuns").text("Stuns: " + hostStuns);
    $("#guestStuns").text("Stuns: " + guestStuns);
}
function validPlace(card1, card2) {
    if (
        card1["value"] - card2["value"] == 1 ||
        card2["value"] - card1["value"] == 1
    ) {
        return true;
    }
    if (card1["value"] == 13 && card2["value"] == 1) {
        return true;
    }
    if (card1["value"] == 1 && card2["value"] == 13) {
        return true;
    }
    return false;
}
function placeCard(hostguestdeckloc, cardIdx, pile) {
    // place a card in the designated position from one's hand.

    if (pile == "left") {
        // mark the card that the new one was placed on as used.
        curDeck[currentLeftCardIdx]["location"] = "used";
        // set the new current index to the card index.
        currentLeftCardIdx = cardIdx;
        // store the hand location of the card to correctly place the next card.
        let handLoc = curDeck[currentLeftCardIdx]["location"];
        // mark the card that was placed as used.
        curDeck[currentLeftCardIdx]["location"] = "used";

        // find the next card in the host or guest's deck and place it in the right spot.
        nextCards = curDeck.filter(
            (card) => card["location"] == hostguestdeckloc
        );
        nextCards.sort((card1, card2) =>
            card1["order"] < card2["order"] ? -1 : 1
        );
        if (nextCards.length == 0) {
            return;
        }
        nextCardIdx = curDeck.findIndex((card) => card == nextCards[0]);

        // set the position of the next card as the former position of the card that was placed.
        curDeck[nextCardIdx]["location"] = handLoc;
    }
    if (pile == "right") {
        // mark the card that the new one was placed on as used.
        curDeck[currentRightCardIdx]["location"] = "used";
        // set the new current index to the card index.
        currentRightCardIdx = cardIdx;
        // store the hand location of the card to correctly place the next card.
        let handLoc = curDeck[currentRightCardIdx]["location"];
        // mark the card that was placed as used.
        curDeck[currentRightCardIdx]["location"] = "used";

        // find the next card in the host or guest's deck and place it in the right spot.
        nextCards = curDeck.filter(
            (card) => card["location"] == hostguestdeckloc
        );
        nextCards.sort((card1, card2) =>
            card1["order"] < card2["order"] ? -1 : 1
        );
        if (nextCards.length == 0) {
            return;
        }
        nextCardIdx = curDeck.findIndex((card) => card == nextCards[0]);

        // set the position of the next card as the former position of the card that was placed.
        curDeck[nextCardIdx]["location"] = handLoc;
    }
}
function stun(player) {
    let countDown = 3;
    // console.log("stunned " + player);
    let stunWho = null;
    if (player == "host") {
        hostStuns++;
        stunWho = "#hostCards";
        freezeHost = true;
    } else if (player == "guest") {
        guestStuns++;
        stunWho = "#guestCards";
        freezeGuest = true;
    }
    $(stunWho).prepend(
        "<div class='stunbanner'> <p>You are stunned!</p> <p class='timer'>" +
        countDown +
        "</p></div>"
    );
    // I apparently need two separate timer vars for both host and guest, otherwise one will interfere with the other.
    if (player == "host") {
        hosttimer = window.setInterval(() => {
            if (countDown == 1) {
                window.clearInterval(hosttimer);
                $(stunWho + " .stunbanner").remove();
            }
            countDown--;
            $(stunWho + " .stunbanner p.timer").text(countDown);
        }, 1000);
    } else if (player == "guest") {
        guesttimer = window.setInterval(() => {
            if (countDown == 1) {
                window.clearInterval(guesttimer);
                $(stunWho + " .stunbanner").remove();
            }
            countDown--;
            $(stunWho + " .stunbanner p.timer").text(countDown);
        }, 1000);
    }
    window.setTimeout(() => {
        if (player == "host") {
            freezeHost = false;
        }
        if (player == "guest") {
            freezeGuest = false;
        }
    }, 3000);
}
function winGame(whowon) {
    freezeAll = true;
    banner(10, whowon + " won!");
    // matchEvents["hostCardsLeft"] = hostCardsLeft;
    // matchEvents["guestCardsLeft"] = guestCardsLeft;
    // matchEvents["hostStuns"] = hostStuns;
    // matchEvents["guestStuns"] = guestStuns;
    // let postData = {
    //     winner: whowon,
    //     hostCardsLeft: hostCardsLeft,
    //     guestCardsLeft: guestCardsLeft,
    //     hostStuns: hostStuns,
    //     guestStuns: guestStuns,
    //     initDeckOrder: initDeckOrder,
    //     matchEvents: matchEvents
    // };
    // console.log(postData);
    // postJSON("submit-play.php", postData);
    window.setTimeout(() => {
      location.assign("match.php?id=" + matchID);
    }, 10000);
}
function checkTiebreak() {
    hostCards = curDeck.filter((card) =>
        card["location"].startsWith("hostCard")
    );
    guestCards = curDeck.filter((card) =>
        card["location"].startsWith("guestCard")
    );

    leftCard = curDeck[currentLeftCardIdx];
    rightCard = curDeck[currentRightCardIdx];
    let needTiebreak = true; // if a card cannot be placed, we need a tiebreak.
    for (let i = 0; i < hostCards.length; i++) {
        if (
            validPlace(hostCards[i]["card"], leftCard["card"]) ||
            validPlace(hostCards[i]["card"], rightCard["card"])
        ) {
            needTiebreak = false; // if a card can be placed, we do not need a tie break.
            break;
        }
    }
    for (let i = 0; i < guestCards.length; i++) {
        if (
            validPlace(guestCards[i]["card"], leftCard["card"]) ||
            validPlace(guestCards[i]["card"], rightCard["card"])
        ) {
            needTiebreak = false; // if a card can be placed, we do not need a tie break.
            break;
        }
    }
    if (needTiebreak) {
        if (!tiebreak()) {
            // if there are no more tiebreaker cards, the game is a draw (since we already check if someone has used all their cards beforehand)
            winGame("draw");
        } else {
            checkTiebreak();
            // do the function again to see if the new cards also don't work.
        }
    }
}
// function // logKeypress(pressedKey) {
//     matchEvents["keystrokes"].push({
//         pressedKey: pressedKey,
//         timestamp: new Date().toISOString().slice(0, 19).replace('T', ' ')
//     });
// }


    function eventLoop(keystrokeIdx) {
      // loop through the keystroke indices, running pressKey for each, at the specified time in matchEvents
      if (keystrokeIdx == 0) {
        window.setTimeout(()=>{
          pressKey(matchEvents["keystrokes"][keystrokeIdx]["pressedKey"]);
          eventLoop(keystrokeIdx + 1);
        }, matchEvents["keystrokes"][keystrokeIdx]["timestamp"]);
      }
      else if (keystrokeIdx == matchEvents["keystrokes"].length) {
        return;
      }
      else {
        window.setTimeout(()=>{
          pressKey(matchEvents["keystrokes"][keystrokeIdx]["pressedKey"]);
          eventLoop(keystrokeIdx + 1);
        }, (matchEvents["keystrokes"][keystrokeIdx]["timestamp"] - matchEvents["keystrokes"][keystrokeIdx - 1]["timestamp"]));
      }
    }
    freezeAll = true;
    banner(5, "Get ready!");
    window.setTimeout(() => {
        freezeAll = false;
        tiebreak();
        refreshPlayfield();
        eventLoop(0);
    }, 5000);
    // matchEvents["startTime"] = new Date().toISOString().slice(0, 19).replace('T', ' '); // stores date as MySql DATETIME obj.
    // matchEvents["keystrokes"] = [];
    function pressKey (pressedKey) {
        // logic for when a user presses a key.
        // console.log("freezeAll: " + freezeAll);
        if (freezeAll) {
            return;
        }

        // console.log(pressedKey);
        if (!freezeHost) {
            // all keys for if the host is not stunned. if the host is not stunned they are able to place cards.

            switch (pressedKey) {
                case 49: // 1
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "hostCard1"
                    );
                    if (nextCard.length == 0) {
                        stun("host");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "right");
                        } else {
                            stun("host");
                        }
                    }
                    break;
                case 50: // 2
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "hostCard2"
                    );
                    if (nextCard.length == 0) {
                        stun("host");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "right");
                        } else {
                            stun("host");
                        }
                    }
                    break;
                case 51: // 3
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "hostCard3"
                    );
                    if (nextCard.length == 0) {
                        stun("host");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "right");
                        } else {
                            stun("host");
                        }
                    }
                    break;
                case 52: // 4
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "hostCard4"
                    );
                    if (nextCard.length == 0) {
                        stun("host");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "right");
                        } else {
                            stun("host");
                        }
                    }
                    break;
                case 53: // 5
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "hostCard5"
                    );
                    if (nextCard.length == 0) {
                        stun("host");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("hostDeck", nextCardIdx, "right");
                        } else {
                            stun("host");
                        }
                    }
                    break;
            }
        }
        if (!freezeGuest) {
            switch (pressedKey) {
                case 111: // o
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "guestCard1"
                    );
                    if (nextCard.length == 0) {
                        stun("guest");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "right");
                        } else {
                            stun("guest");
                        }
                    }
                    break;
                case 112: // p
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "guestCard2"
                    );
                    if (nextCard.length == 0) {
                        stun("guest");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "right");
                        } else {
                            stun("guest");
                        }
                    }
                    break;
                case 91: // [
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "guestCard3"
                    );
                    if (nextCard.length == 0) {
                        stun("guest");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "right");
                        } else {
                            stun("guest");
                        }
                    }
                    break;
                case 93: // ]
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "guestCard4"
                    );
                    if (nextCard.length == 0) {
                        stun("guest");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "right");
                        } else {
                            stun("guest");
                        }
                    }
                    break;
                case 92: // \
                    // logKeypress(pressedKey);
                    nextCard = curDeck.filter(
                        (card) => card["location"] == "guestCard5"
                    );
                    if (nextCard.length == 0) {
                        stun("guest");
                    } else {
                        nextCard = nextCard[0];
                        nextCardIdx = curDeck.findIndex(
                            (card) => card == nextCard
                        );
                        if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentLeftCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "left");
                        } else if (
                            validPlace(
                                nextCard["card"],
                                curDeck[currentRightCardIdx]["card"]
                            )
                        ) {
                            placeCard("guestDeck", nextCardIdx, "right");
                        } else {
                            stun("guest");
                        }
                    }
                    break;
            }
        }
        refreshPlayfield();

        // console.log(curDeck);
        // check if someone has already won the game. if the host has no host cards or the guest has no guest cards, they win.
        hostCardsLeft = curDeck.filter(
            (card) =>
                card["location"] == "hostDeck" ||
                card["location"].startsWith("hostCard")
        );
        guestCardsLeft = curDeck.filter(
            (card) =>
                card["location"] == "guestDeck" ||
                card["location"].startsWith("guestCard")
        );

        if (hostCardsLeft.length == 0) {
            winGame("host");
            return;
        }
        if (guestCardsLeft.length == 0) {
            winGame("guest");
            return;
        }
        // check if all cards can be placed. if not, play a tiebreaker card.
        checkTiebreak();
        // use validPlace to see if all cards are placeable on any pile
        // if not, then check if there are no more tiebreak cards.
        // if no tiebreak cards, draw.
        // if tiebreak cards, set a banner for 3s and tiebreak();
    }
});
