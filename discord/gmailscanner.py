import email, imaplib
from email.parser import FeedParser
from bs4 import BeautifulSoup
# region credentials
username="seventowner@gmail.com"
password="zczsdxewyqwcwslj"
# endregion

mail = imaplib.IMAP4_SSL("imap.gmail.com")
print(mail.login(username, password))


mail.select(mailbox="INBOX", readonly=False)

resp_code, mails = mail.search(None, 'FROM "Twitch" Subject "Your Twitch Login Verification Code"')

mail_ids = mails[0].decode().split()

mail_id = mail_ids[-1]
resp_code, mail_data = mail.fetch(mail_id, '(RFC822)') ## Fetch mail data.
message = email.message_from_bytes(mail_data[0][1]) ## Construct Message from mail data
print("Date : {}".format(message.get("Date")))
print("Code : ", end="")
f = FeedParser()
f.feed(message.as_string())
msg = f.close()
soup = BeautifulSoup(msg.get_payload(decode=True), 'html.parser')
print(soup.find("div", {"class": "header-message-code"}).text)
