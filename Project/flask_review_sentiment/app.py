from flask import Flask, render_template, request
import requests
import json
import os

app = Flask(__name__)

# Load your OpenRouter API key from config
from config import OPENROUTER_API_KEY

@app.route('/', methods=['GET', 'POST'])
def index():
    sentiment = None
    review_text = ""

    if request.method == 'POST':
        review_text = request.form['review']
        sentiment = get_sentiment(review_text)

    return render_template('index.html', sentiment=sentiment, review=review_text)

def get_sentiment(text):
    url = "https://openrouter.ai/api/v1/chat/completions"

    headers = {
        "Authorization": f"Bearer {OPENROUTER_API_KEY}",
        "Content-Type": "application/json"
    }
    prompt = f"Classify the following review as Positive or Negative:\n\"{text}\""

    payload = {
        "model": "openai/gpt-3.5-turbo",
        "messages": [
            {"role": "user", "content": prompt}
        ]
    }

    try:
        response = requests.post(url, headers=headers, data=json.dumps(payload))
        result = response.json()
        reply = result['choices'][0]['message']['content'].strip()
        return reply
    except Exception as e:
        return f"Error: {str(e)}"

if __name__ == '__main__':
    app.run(debug=True)
