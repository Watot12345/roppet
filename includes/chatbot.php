<!-- Roppets AI Chatbot Floating Widget with Vector Mascot Avatar & Professional Font Awesome Icons -->
<div id="roppetsChatbotWidget" style="position: fixed; bottom: 25px; right: 25px; z-index: 99999; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
  
  <!-- Pop-out Animated Greeting Speech Bubble (Appears on Page Load) -->
  <div id="chatGreetingPopup" style="display: none; position: absolute; bottom: 82px; right: 10px; width: 260px; background: #ffffff; padding: 12px 16px; border-radius: 18px 18px 4px 18px; box-shadow: 0 12px 35px rgba(133,46,112,0.25); border: 2px solid var(--primary); animation: popInGreeting 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer;">
    <button type="button" id="btnCloseGreeting" style="position: absolute; top: 4px; right: 8px; background: none; border: none; font-size: 14px; color: #94a3b8; cursor: pointer;">&times;</button>
    <div style="display: flex; gap: 10px; align-items: center;">
      <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
        <img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div>
        <strong style="display: block; font-size: 13px; color: var(--primary);">Mabuhay! Planning a party?</strong>
        <span style="font-size: 11.5px; color: #64748b;">Ask PuppetBot for package rates!</span>
      </div>
    </div>
  </div>

  <!-- Trigger Button with Puppet Avatar & Pulsing Glow -->
  <button type="button" id="roppetsChatTrigger" aria-label="Open Roppets AI Chat" style="width: 68px; height: 68px; border-radius: 50%; background: #ffffff; color: #fff; border: 3px solid #fff; box-shadow: 0 8px 25px rgba(133, 46, 112, 0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; animation: floatTrigger 3.5s ease-in-out infinite; padding: 0; overflow: visible;">
    
    <div id="triggerAvatarWrap" style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center;">
      <img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" id="chatTriggerAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">
      <i class="fa-solid fa-chevron-down" id="chatCloseIcon" style="display: none; font-size: 24px; color: var(--primary);"></i>
    </div>

    <!-- Online Status Dot -->
    <span style="position: absolute; top: 2px; right: 2px; width: 15px; height: 15px; background: #10b981; border: 2.5px solid #fff; border-radius: 50%; box-shadow: 0 0 8px #10b981; z-index: 2;"></span>
  </button>

  <!-- Chat Window Box -->
  <div id="roppetsChatBox" style="display: none; width: 385px; max-width: calc(100vw - 30px); height: 540px; max-height: calc(100vh - 100px); background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); border: 1px solid rgba(133,46,112,0.18); overflow: hidden; flex-direction: column; position: absolute; bottom: 82px; right: 0; animation: slideUpChat 0.35s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <!-- Chat Header -->
    <div style="background: linear-gradient(135deg, var(--primary) 0%, #2f1d3a 100%); color: #fff; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div id="botHeaderAvatar" style="width: 44px; height: 44px; border-radius: 50%; background: #fff; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 10px rgba(0,0,0,0.25); transition: transform 0.3s ease; border: 2px solid #fff;">
          <img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div>
          <div style="font-weight: 700; font-size: 15.5px; display: flex; align-items: center; gap: 6px;">
            PuppetBot <i class="fa-solid fa-masks-theater" style="font-size: 13px; color: var(--secondary-light);"></i>
            <span style="background: #10b981; color: #fff; font-size: 9px; padding: 1px 6px; border-radius: 10px; font-weight: 700; letter-spacing: 0.5px;">ONLINE</span>
          </div>
          <small style="color: #cbd5e1; font-size: 11px;">Roppets Edutainment AI Guide</small>
        </div>
      </div>
      <div style="display: flex; gap: 6px;">
        <button type="button" id="btnChatReset" title="Reset Chat" style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 14px; padding: 6px;"><i class="fa-solid fa-rotate-right"></i></button>
        <button type="button" id="btnChatClose" title="Close Chat" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 18px; padding: 6px;"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    <!-- Message History Area -->
    <div id="roppetsChatMessages" style="flex: 1; padding: 16px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 12px; font-size: 13.5px; line-height: 1.55;">
      
      <!-- Welcome Message from Bot -->
      <div class="chat-msg-row bot-msg-row">
        <div class="bot-avatar-bubble">
          <img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="bot-bubble">
          Mabuhay! I am <strong>PuppetBot</strong>, your Roppets Edutainment Guide. How can I help make your celebration or corporate event extra memorable today?
        </div>
      </div>

      <!-- Quick Suggestion Chips with Font Awesome Icons -->
      <div id="chatSuggestionChips" style="display: flex; flex-wrap: wrap; gap: 6px; margin-left: 38px; animation: fadeIn 0.4s ease;">
        <button type="button" class="chat-chip" data-msg="What are your party packages and rates?"><i class="fa-solid fa-cake-candles"></i> Party Packages</button>
        <button type="button" class="chat-chip" data-msg="Tell me about the Bubble Factory Show"><i class="fa-solid fa-soap"></i> Bubble Show</button>
        <button type="button" class="chat-chip" data-msg="How does Hand Puppet Show work?"><i class="fa-solid fa-masks-theater"></i> Hand Puppet Plays</button>
        <button type="button" class="chat-chip" data-msg="How do I get a custom mascot made?"><i class="fa-solid fa-paw"></i> Custom Mascots</button>
        <button type="button" class="chat-chip" data-msg="How can I track my booking?"><i class="fa-solid fa-magnifying-glass"></i> Track Booking</button>
      </div>

    </div>

    <!-- Animated Typing Indicator with Bouncing Dots -->
    <div id="chatTypingIndicator" style="display: none; padding: 8px 18px; background: #f8fafc; font-size: 12px; color: var(--primary);">
      <div style="display: flex; align-items: center; gap: 8px;">
        <div class="bot-avatar-bubble" style="width: 26px; height: 26px;">
          <img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="typing-bubble">
          <span class="typing-dot"></span>
          <span class="typing-dot"></span>
          <span class="typing-dot"></span>
        </div>
        <small style="color: #64748b; font-weight: 500;">PuppetBot is thinking...</small>
      </div>
    </div>

    <!-- Chat Input Area -->
    <form id="roppetsChatInputForm" style="padding: 12px 14px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; align-items: center;">
      <input type="text" id="chatInputText" placeholder="Ask about shows, packages, mascots..." autocomplete="off" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 24px; padding: 10px 16px; font-size: 13.5px; outline: none; font-family: inherit; transition: border-color 0.2s;">
      <button type="submit" id="btnChatSend" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: transform 0.2s, background 0.2s;">
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </form>

  </div>
</div>

<style>
  /* Animations */
  @keyframes slideUpChat {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }
  @keyframes popInGreeting {
    0% { opacity: 0; transform: scale(0.6) translateY(20px); }
    70% { transform: scale(1.05) translateY(-5px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
  }
  @keyframes floatTrigger {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-7px); }
  }
  @keyframes msgPop {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes dotBounce {
    0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
  }
  @keyframes avatarDance {
    0%, 100% { transform: rotate(0deg) scale(1); }
    25% { transform: rotate(-8deg) scale(1.08); }
    75% { transform: rotate(8deg) scale(1.08); }
  }

  .chat-msg-row {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    animation: msgPop 0.3s ease;
  }
  .bot-avatar-bubble {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    margin-top: 2px;
    border: 1px solid var(--primary);
    background: #fff;
  }
  .bot-bubble {
    background: #ffffff;
    color: #1e293b;
    padding: 12px 14px;
    border-radius: 4px 14px 14px 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    border: 1px solid #e2e8f0;
    max-width: 85%;
    line-height: 1.55;
  }
  .user-bubble {
    background: linear-gradient(135deg, var(--primary) 0%, #913a7c 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 14px 4px 14px 14px;
    max-width: 80%;
    box-shadow: 0 2px 8px rgba(133,46,112,0.25);
    margin-left: auto;
    animation: msgPop 0.3s ease;
  }

  .typing-bubble {
    background: #ffffff;
    padding: 8px 12px;
    border-radius: 12px;
    display: flex;
    gap: 4px;
    border: 1px solid #e2e8f0;
    width: fit-content;
  }
  .typing-dot {
    width: 6px;
    height: 6px;
    background: var(--primary);
    border-radius: 50%;
    animation: dotBounce 1.4s infinite ease-in-out both;
  }
  .typing-dot:nth-child(1) { animation-delay: -0.32s; }
  .typing-dot:nth-child(2) { animation-delay: -0.16s; }

  .chat-chip {
    background: #ffffff;
    border: 1px solid rgba(133,46,112,0.3);
    color: var(--primary);
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .chat-chip i {
    font-size: 11px;
    color: var(--secondary);
  }
  .chat-chip:hover {
    background: var(--primary);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(133,46,112,0.2);
  }
  .chat-chip:hover i {
    color: #ffffff;
  }
  #roppetsChatTrigger:hover {
    transform: scale(1.08) !important;
  }
  #chatInputText:focus {
    border-color: var(--primary) !important;
  }
  #btnChatSend:hover {
    transform: scale(1.08);
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('roppetsChatTrigger');
    const chatBox = document.getElementById('roppetsChatBox');
    const closeBtn = document.getElementById('btnChatClose');
    const resetBtn = document.getElementById('btnChatReset');
    const form = document.getElementById('roppetsChatInputForm');
    const input = document.getElementById('chatInputText');
    const messages = document.getElementById('roppetsChatMessages');
    const typingIndicator = document.getElementById('chatTypingIndicator');
    const avatarImg = document.getElementById('chatTriggerAvatarImg');
    const closeIcon = document.getElementById('chatCloseIcon');
    const greetingPopup = document.getElementById('chatGreetingPopup');
    const btnCloseGreeting = document.getElementById('btnCloseGreeting');
    const botHeaderAvatar = document.getElementById('botHeaderAvatar');

    let chatHistory = [];

    // 1. Show Greeting Bubble after 2.2 seconds on page load
    setTimeout(() => {
      if (chatBox.style.display !== 'flex' && greetingPopup) {
        greetingPopup.style.display = 'block';
      }
    }, 2200);

    if (btnCloseGreeting) {
      btnCloseGreeting.addEventListener('click', (e) => {
        e.stopPropagation();
        greetingPopup.style.display = 'none';
      });
    }

    if (greetingPopup) {
      greetingPopup.addEventListener('click', () => {
        greetingPopup.style.display = 'none';
        openChat();
      });
    }

    function openChat() {
      chatBox.style.display = 'flex';
      avatarImg.style.display = 'none';
      closeIcon.style.display = 'block';
      if (greetingPopup) greetingPopup.style.display = 'none';
      input.focus();
    }

    function toggleChat() {
      if (chatBox.style.display === 'none' || !chatBox.style.display) {
        openChat();
      } else {
        chatBox.style.display = 'none';
        avatarImg.style.display = 'block';
        closeIcon.style.display = 'none';
      }
    }

    if (trigger) trigger.addEventListener('click', toggleChat);
    if (closeBtn) closeBtn.addEventListener('click', toggleChat);

    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        chatHistory = [];
        messages.innerHTML = `
          <div class="chat-msg-row bot-msg-row">
            <div class="bot-avatar-bubble"><img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;"></div>
            <div class="bot-bubble">
              Chat reset. How can I help you with Roppets shows, party packages, or custom mascots?
            </div>
          </div>
          <div id="chatSuggestionChips" style="display: flex; flex-wrap: wrap; gap: 6px; margin-left: 38px;">
            <button type="button" class="chat-chip" data-msg="What are your party packages and rates?"><i class="fa-solid fa-cake-candles"></i> Party Packages</button>
            <button type="button" class="chat-chip" data-msg="Tell me about the Bubble Factory Show"><i class="fa-solid fa-soap"></i> Bubble Show</button>
            <button type="button" class="chat-chip" data-msg="How does Hand Puppet Show work?"><i class="fa-solid fa-masks-theater"></i> Hand Puppet Plays</button>
            <button type="button" class="chat-chip" data-msg="How do I get a custom mascot made?"><i class="fa-solid fa-paw"></i> Custom Mascots</button>
            <button type="button" class="chat-chip" data-msg="How can I track my booking?"><i class="fa-solid fa-magnifying-glass"></i> Track Booking</button>
          </div>
        `;
        bindChips();
      });
    }

    function bindChips() {
      document.querySelectorAll('.chat-chip').forEach(chip => {
        chip.addEventListener('click', () => {
          const msg = chip.getAttribute('data-msg');
          if (msg) sendMessage(msg);
        });
      });
    }
    bindChips();

    function formatMarkdown(text) {
      return text
        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="chat-link" style="color: var(--primary); font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i> $1</a>')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`([^`]+)`/g, '<code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 12px; color: var(--primary);">$1</code>')
        .replace(/\n/g, '<br>');
    }

    function appendUserMessage(text) {
      const div = document.createElement('div');
      div.className = 'user-bubble';
      div.textContent = text;
      messages.appendChild(div);
      messages.scrollTop = messages.scrollHeight;
    }

    // Typewriter effect animation for bot responses
    function typeWriterMessage(formattedHtml, rawText) {
      const row = document.createElement('div');
      row.className = 'chat-msg-row bot-msg-row';
      
      const avatar = document.createElement('div');
      avatar.className = 'bot-avatar-bubble';
      avatar.innerHTML = '<img src="assets/images/puppet_bot_avatar.svg" alt="PuppetBot" style="width: 100%; height: 100%; object-fit: cover;">';

      const bubble = document.createElement('div');
      bubble.className = 'bot-bubble';

      row.appendChild(avatar);
      row.appendChild(bubble);
      messages.appendChild(row);

      const plainWords = formattedHtml.split(' ');
      let wordIdx = 0;
      bubble.innerHTML = '';

      const timer = setInterval(() => {
        if (wordIdx < plainWords.length) {
          bubble.innerHTML += (wordIdx > 0 ? ' ' : '') + plainWords[wordIdx];
          wordIdx++;
          messages.scrollTop = messages.scrollHeight;
        } else {
          clearInterval(timer);
          if (botHeaderAvatar) botHeaderAvatar.style.animation = '';
        }
      }, 25);
    }

    async function sendMessage(text) {
      if (!text.trim()) return;
      appendUserMessage(text);
      input.value = '';

      chatHistory.push({ sender: 'user', text: text });
      typingIndicator.style.display = 'block';
      if (botHeaderAvatar) botHeaderAvatar.style.animation = 'avatarDance 1.2s infinite ease-in-out';
      messages.scrollTop = messages.scrollHeight;

      try {
        const res = await fetch('api/chatbot.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: text, history: chatHistory })
        });
        const data = await res.json();
        typingIndicator.style.display = 'none';

        if (data.success && data.reply) {
          const html = formatMarkdown(data.reply);
          typeWriterMessage(html, data.reply);
          chatHistory.push({ sender: 'model', text: data.reply });
        } else {
          typeWriterMessage('Sorry, I encountered an issue. Please call our office at (02) 8912-3456 or tap "Book a Show" above.');
        }
      } catch (err) {
        typingIndicator.style.display = 'none';
        if (botHeaderAvatar) botHeaderAvatar.style.animation = '';
        typeWriterMessage('Connection error. You can also reach our team at +63 917 530 0823.');
      }
    }

    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(input.value);
      });
    }
  });
</script>
