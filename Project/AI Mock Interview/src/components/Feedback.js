import React from "react";

function Feedback({ feedback }) {
  return (
    <div className="feedback">
      <h4>Feedback</h4>
      <p>{feedback}</p>
    </div>
  );
}

export default Feedback;