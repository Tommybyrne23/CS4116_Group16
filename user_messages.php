<?php
session_start();
require_once 'functions.php';
isLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SportzWorld - Message Center</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background:rgb(17, 130, 235);
      margin: 0;
      padding: 0;
    }
    header {
            background-color: #333;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        nav button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-left: 10px;
            cursor: pointer;
        }
    .messages-container {
      max-width: 1100px;
      margin: 60px auto;
      padding: 20px;
    }
    .messages-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 32px;
    }
    .message-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
      padding: 36px 28px;
      width: 320px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.15s;
    }
    .message-card:hover {
      transform: translateY(-6px) scale(1.03);
    }
    .message-title {
      font-size: 1.2rem;
      font-weight: bold;
      margin-bottom: 18px;
      color: #222;
      text-align: center;
    }
    .message-desc {
      font-size: 1rem;
      color: black;
      margin-bottom: 26px;
      text-align: center;
    }
    .message-btn {
      background: #1976d2;
      color: #fff;
      border: none;
      border-radius: 24px;
      padding: 10px 36px;
      font-size: 1rem;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.18s;
      display: inline-block;
    }
    .message-btn:hover {
      background: #125ea2;
    }
    @media (max-width: 1000px) {
      .messages-row {
        flex-direction: column;
        align-items: center;
      }
      .message-card {
        width: 90%;
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
<header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='homepage.php'">Logout</button>
            <button onclick="location.href='user_home.php'">Home</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='reviews.php'">Reviews</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

  <div class="messages-container">
    <div class="messages-row">
      <div class="message-card">
        <div class="message-title">Message Businesses</div>
        <div class="message-desc">Contact businesses directly to make inquiries, negotiate prices and discuss services.</div>
        <a href="user_to_business_messages.php" class="message-btn">Go</a>
      </div>
      <div class="message-card">
        <div class="message-title">Message Users</div>
        <div class="message-desc">Connect with other users to discuss experiences or share recommendations.</div>
        <a href="user_to_user_messages.php" class="message-btn">Go</a>
      </div>
      <div class="message-card">
        <div class="message-title">Message Requests</div>
        <div class="message-desc">View and approve requests from users who want to connect with you.</div>
        <a href="message_request_approval.php" class="message-btn">Go</a>
      </div>
    </div>
  </div>
</body>
</html>


