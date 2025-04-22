from flask import Flask, request, jsonify
from transformers import pipeline

# Load the models once
fake_review_pipe =  pipeline("text-classification", model="theArijitDas/distilbert-finetuned-fake-reviews")     
sentiment_pipe = pipeline("sentiment-analysis")  # You can switch to TextBlob or Vader if preferred

app = Flask(__name__)

@app.route('/analyze-review', methods=['POST'])
def analyze_review():
    data = request.get_json()
    review = data.get('text', '')

    # Run models
    fake_result = fake_review_pipe(review)[0]
    sentiment_result = sentiment_pipe(review)[0]

    response = {
        'sentiment': sentiment_result['label'].lower(),
        'is_fake': 'yes' if fake_result['label'] == 'Fake' else 'no',
        'confidence': fake_result['score']
    }
    return jsonify(response)

if __name__ == '__main__':
    app.run(port=5000)
