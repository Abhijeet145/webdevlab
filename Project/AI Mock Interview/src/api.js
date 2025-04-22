import axios from "axios";

const GEMINI_API_KEY = process.env.REACT_APP_GEMINI_API_KEY;
const GEMINI_API_URL =
  "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCClf0cwfHH8HBBCnNDlEhwM6GlRzQMd"; // Replace with the actual Gemini API URL

export const generateQuestions = async (topic) => {
  const req = `generate 5 Questions on` + topic;
  const res = await axios.post(
    `${GEMINI_API_URL}`,
    { req },
    {
      headers: {
        Authorization: `Bearer ${GEMINI_API_KEY}`,
      },
    }
  );
  return res.data;
};

export const evaluateAnswer = async (questionId, answer) => {
  const res = await axios.post(
    `${GEMINI_API_URL}/evaluate-answer`,
    { questionId, answer },
    {
      headers: {
        Authorization: `Bearer ${GEMINI_API_KEY}`,
      },
    }
  );
  return res.data;
};
