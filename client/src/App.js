import React, { useState, useEffect, useCallback } from "react";

function App() {
  const [roomCode, setRoomCode] = useState("");
  const [joinCodeInput, setJoinCodeInput] = useState("");
  const [username, setUsername] = useState("");
  const [usernameInput, setUsernameInput] = useState("");
  const [message, setMessage] = useState("");
  const [messages, setMessages] = useState([]);
  const [isTyping, setIsTyping] = useState(false);

  // 🔐 Authentication states
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const fetchMessages = useCallback(async () => {
    try {
      const res = await fetch(`http://localhost/streamsync/api/get_messages.php?room=${roomCode}`);
      const data = await res.json();
      setMessages(data);
    } catch (err) {
      console.error("Failed to fetch messages:", err);
    }
  }, [roomCode]);

  useEffect(() => {
    if (!roomCode) return;
    fetchMessages();
    const interval = setInterval(() => {
      fetchMessages();
    }, 3000);
    return () => clearInterval(interval);
  }, [roomCode, fetchMessages]);

 const handleLogin = async () => {
  if (!email.trim() || !password.trim()) {
    alert("Please fill in both email and password.");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert("Please enter a valid email address.");
    return;
  }

  try {
    const res = await fetch("http://localhost/streamsync/api/login_user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });
    const data = await res.json();
    if (data.success) {
      setIsLoggedIn(true);
      setUsernameInput(data.username || "");
      alert("Login successful");
    } else {
      alert("Login failed: " + data.message);
    }
  } catch (err) {
    console.error("Login error", err);
    alert("Login request failed.");
  }
};

const handleRegister = async () => {
  if (!email.trim() || !password.trim()) {
    alert("Please fill in both email and password.");
    return;
  }

  try {
    const res = await fetch("http://localhost/streamsync/api/register_user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });

    const text = await res.text();
    console.log("📥 Raw response from register_user.php:", text);
    console.log("📤 Sent email:", email);
    console.log("📤 Sent password:", password);

    const data = JSON.parse(text);
    if (data.success) {
      alert("Registration successful. You can now log in.");
    } else {
      alert("Registration failed: " + data.message);
    }
  } catch (err) {
    console.error("Registration error:", err.message || err);
    alert("Registration request failed.");
  }
};

  const handleCreateRoom = async () => {
    try {
      const res = await fetch("http://localhost/streamsync/api/create_room.php");
      const data = await res.json();
      if (data.room_code && data.token) {
        setRoomCode(data.room_code);
        localStorage.setItem("roomCode", data.room_code);
        localStorage.setItem("roomToken", data.token);
        alert("Room created!");
      } else {
        alert("Failed to create room.");
      }
    } catch (err) {
      console.error("Create Room Error:", err);
      alert("Error creating room.");
    }
  };

  const handleJoinRoom = async (code) => {
    if (!code.trim()) return alert("Please enter a room code.");
    try {
      const res = await fetch("http://localhost/streamsync/api/join_room.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ room_code: code }),
      });
      const data = await res.json();
      if (data.exists) {
        setRoomCode(code);
        localStorage.setItem("roomCode", code);
        localStorage.setItem("roomToken", data.token);
        alert("Joined room!");
      } else {
        alert("Room not found.");
      }
    } catch (err) {
      console.error("Join Room Error:", err);
      alert("Error joining room.");
    }
  };

  const handleLoadRoom = () => {
    const savedCode = localStorage.getItem("roomCode");
    const savedToken = localStorage.getItem("roomToken");
    if (savedCode && savedToken) {
      setRoomCode(savedCode);
      alert("Previous room loaded!");
    } else {
      alert("No room found in storage.");
    }
  };

  const handleLeaveRoom = () => {
    localStorage.removeItem("roomCode");
    localStorage.removeItem("roomToken");
    setRoomCode("");
    setMessages([]);
    setMessage("");
    alert("You have left the room.");
  };

  const sendMessage = async () => {
    if (!message.trim()) return;
    try {
      await fetch("http://localhost/streamsync/api/save_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          room_code: roomCode,
          token: localStorage.getItem("roomToken"),
          username: username,
          message: message,
        }),
      });
      setMessage("");
      fetchMessages();
    } catch (err) {
      console.error("Failed to send message:", err);
    }
  };

  return (
    <div style={{
      padding: "30px",
      fontFamily: "Arial, sans-serif",
      backgroundColor: "#1a1a1a",
      color: "#f0f0f0",
      minHeight: "100vh",
      textAlign: "center"
    }}>
      <img
        src="/streamsync.png"
        alt="StreamSync Logo"
        style={{
          width: "100px",
          height: "100px",
          marginBottom: "20px",
          borderRadius: "12px",
          boxShadow: "0 0 15px #00ffff"
        }}
      />
      <h1 style={{ fontSize: "48px", color: "#00ffff", marginBottom: "30px" }}>StreamSync</h1>

      {!isLoggedIn ? (
        <>
          <h2>Login or Register</h2>
          <input
            type="text"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            style={{ padding: "10px", marginBottom: "10px", fontSize: "16px", borderRadius: "6px" }}
          /><br />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            style={{ padding: "10px", marginBottom: "10px", fontSize: "16px", borderRadius: "6px" }}
          /><br />
          <button
            onClick={handleLogin}
            style={{ marginRight: "10px", padding: "10px 20px", fontSize: "16px", backgroundColor: "#00ccff", border: "none", borderRadius: "6px", cursor: "pointer" }}
          >
            Login
          </button>
          <button
            onClick={handleRegister}
            style={{ padding: "10px 20px", fontSize: "16px", backgroundColor: "#00ff99", border: "none", borderRadius: "6px", cursor: "pointer" }}
          >
            Register
          </button>
        </>
      ) : !username ? (
        <>
          <input
            type="text"
            placeholder="Enter your name"
            value={usernameInput}
            onChange={(e) => setUsernameInput(e.target.value)}
            style={{ padding: "10px", marginRight: "10px", fontSize: "16px", borderRadius: "6px" }}
          />
          <button
            onClick={() => {
              if (!usernameInput.trim()) return alert("Please enter your name.");
              setUsername(usernameInput);
            }}
            style={{ padding: "10px 20px", fontSize: "16px", backgroundColor: "#00ffff", border: "none", borderRadius: "6px", cursor: "pointer" }}
          >
            Start
          </button>
        </>
      ) : !roomCode ? (
        <>
          <button onClick={handleCreateRoom} style={{ marginBottom: "10px", padding: "10px 20px", backgroundColor: "#00ffff", border: "none", borderRadius: "6px", fontSize: "16px", cursor: "pointer" }}>
            Create Room
          </button>

          <div style={{ marginTop: "10px" }}>
            <input
              type="text"
              placeholder="Enter Room Code"
              value={joinCodeInput}
              onChange={(e) => setJoinCodeInput(e.target.value)}
              style={{ padding: "10px", marginRight: "10px", fontSize: "16px", borderRadius: "6px" }}
            />
            <button
              onClick={() => handleJoinRoom(joinCodeInput)}
              style={{ padding: "10px 20px", fontSize: "16px", backgroundColor: "#00ffff", border: "none", borderRadius: "6px", cursor: "pointer" }}
            >
              Join Room
            </button>
          </div>

          <div style={{ marginTop: "10px" }}>
            <button onClick={handleLoadRoom} style={{ padding: "10px 20px", fontSize: "16px", backgroundColor: "#555", border: "none", borderRadius: "6px", color: "#fff", cursor: "pointer" }}>
              Load Previous Room
            </button>
          </div>
        </>
      ) : (
        <>
          <h2>Room: {roomCode}</h2>
          <p>Welcome, {username}!</p>

          <div style={{ marginTop: "10px" }}>
            <input
              type="text"
              placeholder="Type a message"
              value={message}
              onChange={(e) => {
                setMessage(e.target.value);
                setIsTyping(true);
                clearTimeout(window.typingTimeout);
                window.typingTimeout = setTimeout(() => setIsTyping(false), 1000);
              }}
              style={{ padding: "10px", width: "60%", fontSize: "16px", borderRadius: "6px" }}
            />
            <button onClick={sendMessage} style={{ marginLeft: "10px", padding: "10px 20px", fontSize: "16px", backgroundColor: "#00ffff", border: "none", borderRadius: "6px", cursor: "pointer" }}>
              Send
            </button>
          </div>

          <div style={{ marginTop: "10px" }}>
            <button onClick={handleLeaveRoom} style={{ padding: "10px 20px", fontSize: "16px", backgroundColor: "#d9534f", color: "#fff", border: "none", borderRadius: "6px", cursor: "pointer" }}>
              Leave Room
            </button>
          </div>

          {isTyping && <p><em>{username} is typing...</em></p>}

          <div style={{ marginTop: "20px", textAlign: "left", maxWidth: "600px", margin: "20px auto" }}>
            <h3>Chat Messages:</h3>
            <ul style={{ listStyle: "none", padding: 0 }}>
              {messages.map((msg, index) => (
                <li key={index} style={{ background: "#333", padding: "10px", borderRadius: "6px", marginBottom: "10px" }}>
                  <strong style={{ color: "#00ffff" }}>{msg.username}</strong>: {msg.message}
                </li>
              ))}
            </ul>
          </div>
        </>
      )}
    </div>
  );
}

export default App;
