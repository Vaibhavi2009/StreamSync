import React, { useState, useEffect } from "react";

function App() {
  const [roomCode, setRoomCode] = useState("");
  const [username, setUsername] = useState("Player1");
  const [message, setMessage] = useState("");
  const [messages, setMessages] = useState([]);

  // 1. Create Room
  const handleCreateRoom = async () => {
    try {
      const res = await fetch("http://localhost/streamsync/api/create_room.php");
      const data = await res.json();
      setRoomCode(data.room_code);
    } catch (err) {
      console.error("Failed to create room:", err);
    }
  };

  // 2. Send Chat Message
  const sendMessage = async () => {
    if (!message.trim()) return;
    try {
      await fetch("http://localhost/streamsync/api/save_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          room_code: roomCode,
          username,
          message,
        }),
      });
      setMessage(""); // clear input
    } catch (err) {
      console.error("Failed to send message:", err);
    }
  };

  // 3. Fetch Messages
  const fetchMessages = async () => {
    if (!roomCode) return;
    try {
      const res = await fetch(`http://localhost/streamsync/api/get_messages.php?room_code=${roomCode}`);
      const data = await res.json();
      setMessages(data);
    } catch (err) {
      console.error("Error fetching messages:", err);
    }
  };

  // Auto-refresh messages every 2 seconds
  useEffect(() => {
    const interval = setInterval(fetchMessages, 2000);
    return () => clearInterval(interval);
  }, [roomCode]);

  return (
    <div style={{ padding: "20px", fontFamily: "Arial" }}>
      <h1>StreamSync Chat</h1>

      {!roomCode ? (
        <>
          <button onClick={handleCreateRoom}>Create Room</button>
        </>
      ) : (
        <div>
          <h2>Room Code: {roomCode}</h2>

          <div style={{ marginBottom: "10px" }}>
            <input
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              placeholder="Type a message..."
              style={{ padding: "8px", width: "250px" }}
            />
            <button onClick={sendMessage} style={{ marginLeft: "10px" }}>
              Send
            </button>
          </div>

          <div>
            <h3>Chat Messages:</h3>
            <ul>
              {messages.map((msg, index) => (
                <li key={index}>
                  <strong>{msg.username}:</strong> {msg.message}
                </li>
              ))}
            </ul>
          </div>
        </div>
      )}
    </div>
  );
}

export default App;
