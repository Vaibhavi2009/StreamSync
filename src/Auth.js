import React, { useState } from "react";

function Auth({ onLoginSuccess }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [isRegister, setIsRegister] = useState(false);

  const handleSubmit = async () => {
    const url = isRegister
      ? "http://localhost/streamsync/api/register.php"
      : "http://localhost/streamsync/api/login.php";

    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password }),
    });

    const data = await res.json();

    if (data.success) {
      localStorage.setItem("authToken", data.token);
      localStorage.setItem("username", data.username);
      alert(isRegister ? "Registered!" : "Logged in!");
      if (!isRegister) onLoginSuccess(data.username);
    } else {
      alert(data.error);
    }
  };

  return (
    <div style={{ padding: 20 }}>
      <h2>{isRegister ? "Register" : "Login"}</h2>
      <input
        type="text"
        placeholder="Username"
        value={username}
        onChange={(e) => setUsername(e.target.value)}
        style={{ margin: 5 }}
      />
      <br />
      <input
        type="password"
        placeholder="Password (min 6 chars)"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        style={{ margin: 5 }}
      />
      <br />
      <button onClick={handleSubmit}>{isRegister ? "Register" : "Login"}</button>
      <p>
        {isRegister ? "Already have an account?" : "New user?"}{" "}
        <span
          onClick={() => setIsRegister(!isRegister)}
          style={{ color: "blue", cursor: "pointer" }}
        >
          {isRegister ? "Login here" : "Register here"}
        </span>
      </p>
    </div>
  );
}

export default Auth;
