import React, { useState } from "react";
import { GoogleGenAI } from "@google/genai";
import Feedback from "./Feedback";

const ai = new GoogleGenAI({
  apiKey: "AIzaSyCClf0cwfHH8HBBCnNDlEhwM6GlRzQMd-o",
});

function QuestionGenerator() {
  const [topic, setTopic] = useState("");
  const [questions, setQuestions] = useState([]);
  const [currentAnswers, setCurrentAnswers] = useState({});
  const [feedbacks, setFeedbacks] = useState({});
  const [loadingQuestionId, setLoadingQuestionId] = useState(null);
  let i = 1;
  async function getQuestions() {
    const response = await ai.models.generateContent({
      model: "gemini-2.0-flash",
      contents: `Give me 5 questions on ${topic}. Give the response in array of object(id, text).`,
    });

    return response.text;
  }

  async function getFeedback(questionText, answer) {
    const response = await ai.models.generateContent({
      model: "gemini-2.0-flash",
      contents: `Given a question and a user's answer, evaluate it as an interview on ${topic}. Question: ${questionText} Answer: ${answer}. Give feedback (not more than 100 words).`,
    });

    return response.text;
  }

  const fetchQuestions = async () => {
    try {
      let quesText = await getQuestions();
      quesText = quesText.substring(7, quesText.length - 5);
      const parsedQuestions = JSON.parse(quesText);
      setQuestions(parsedQuestions);
      setCurrentAnswers({});
      setFeedbacks({});
    } catch (err) {
      console.error("Error fetching questions:", err);
      alert("Failed to fetch questions. Please try again.");
    }
  };

  const handleAnswerChange = (questionId, value) => {
    setCurrentAnswers((prev) => ({
      ...prev,
      [questionId]: value,
    }));
  };

  const submitAnswer = async (questionId) => {
    try {
      setLoadingQuestionId(questionId);
      const questionObj = questions.find((q) => q.id === questionId);
      const answer = currentAnswers[questionId] || "";
      const res = await getFeedback(questionObj.text, answer);

      setFeedbacks((prev) => ({
        ...prev,
        [questionId]: res,
      }));
    } catch (err) {
      console.error("Error submitting answer:", err);
      alert("Failed to submit the answer. Please try again.");
    } finally {
      setLoadingQuestionId(null);
    }
  };

  const formatFeedback = (feedback) => {
    const [summary, ...details] = feedback.split("**Feedback:**");
    return (
      <div>
        <p><strong style={{ color: "#2e7d32" }}>{summary.trim()}</strong></p>
        {details.length > 0 && (
          <p style={{ marginTop: "0.5rem", color: "#333" }}>{details.join("").trim()}</p>
        )}
      </div>
    );
  };

  return (
    <div>
      <h2>Generate Questions</h2>
      <input
        type="text"
        placeholder="Enter topic (e.g., React, JavaScript...)"
        value={topic}
        onChange={(e) => setTopic(e.target.value)}
      />
      <button onClick={fetchQuestions}>Generate Questions</button>

      {questions.length > 0 && (
        <div>
          <h3> Questions</h3>
          {questions.map((q) => (
            <div key={q.id} className="question">
              <p>{i++}. {q.text}</p>
              <textarea
                placeholder="Your answer..."
                value={currentAnswers[q.id] || ""}
                onChange={(e) => handleAnswerChange(q.id, e.target.value)}
              />
              <button
                onClick={() => submitAnswer(q.id)}
                disabled={loadingQuestionId === q.id}
              >
                {loadingQuestionId === q.id ? (
                  <span className="loader" />
                ) : (
                  "Submit Answer"
                )}
              </button>

              {feedbacks[q.id] && (
                <div className="feedback-box">
                  {formatFeedback(feedbacks[q.id])}
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {/* Styles */}
      <style>{`
        .feedback-box {
          background-color: #f0fdf4;
          border-left: 4px solid #2e7d32;
          padding: 1rem;
          margin-top: 0.5rem;
          border-radius: 6px;
          font-size: 0.95rem;
        }

        .loader {
          display: inline-block;
          width: 16px;
          height: 16px;
          border: 2px solid #ccc;
          border-top: 2px solid #333;
          border-radius: 50%;
          animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
          to {
            transform: rotate(360deg);
          }
        }

        textarea {
          width: 100%;
          min-height: 60px;
          margin-top: 0.5rem;
          padding: 0.5rem;
          border-radius: 6px;
          border: 1px solid #ccc;
        }

        button {
          margin-top: 0.5rem;
          padding: 0.5rem 1rem;
          background-color: #1976d2;
          color: white;
          border: none;
          border-radius: 4px;
          cursor: pointer;
        }

        button:disabled {
          background-color: #aaa;
          cursor: not-allowed;
        }

        input[type="text"] {
          padding: 0.5rem;
          margin-right: 0.5rem;
          border: 1px solid #ccc;
          border-radius: 4px;
        }

        .question {
          margin-bottom: 1.5rem;
        }
      `}</style>
    </div>
  );
}

export default QuestionGenerator;
