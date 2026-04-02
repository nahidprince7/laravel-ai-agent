<script setup lang="ts">
import { ref, nextTick, onMounted, watch } from 'vue'

// ============================================
// TYPES & INTERFACES
// ============================================

interface Message {
  id: number
  text: string
  sender: 'user' | 'support'
  timestamp: Date
}

// ============================================
// REACTIVE STATE
// ============================================

const isOpen = ref(false)
const messages = ref<Message[]>([])
const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)
const isTyping = ref(false)
const unreadCount = ref(1)

// ============================================
// DUMMY DATA INITIALIZATION
// ============================================

onMounted(() => {
  // Initialize with dummy messages
  messages.value = [
    {
      id: 1,
      text: 'Hello! How can I help you today?',
      sender: 'support',
      timestamp: new Date(Date.now() - 300000) // 5 minutes ago
    },
    {
      id: 2,
      text: 'Hi! I have a question about my account.',
      sender: 'user',
      timestamp: new Date(Date.now() - 240000) // 4 minutes ago
    },
    {
      id: 3,
      text: 'Of course! I\'d be happy to help. What would you like to know?',
      sender: 'support',
      timestamp: new Date(Date.now() - 180000) // 3 minutes ago
    }
  ]
})

// ============================================
// UTILITY FUNCTIONS
// ============================================

const sendMessage = async (): Promise<void> => {
  if (!newMessage.value.trim()) return

  // Add user message (keep your existing code)
  const userMessage: Message = {
    id: Date.now(),
    text: newMessage.value.trim(),
    sender: 'user',
    timestamp: new Date()
  }
  messages.value.push(userMessage)
  const messageText = newMessage.value.trim()
  newMessage.value = ''
  scrollToBottom()

  // Show typing indicator
  isTyping.value = true
  scrollToBottom()

  // Call your Laravel route
  const response = await fetch('/ai-agent', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    },
    body: JSON.stringify({ message: messageText })
  })
  const data = await response.json()
  isTyping.value = false

  messages.value.push({
    id: Date.now() + 1,
    text: data.reply,   // make sure your controller returns { reply: '...' }
    sender: 'support',
    timestamp: new Date()
  })
  scrollToBottom()
}


const formatTime = (date: Date): string => {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const scrollToBottom = async (): Promise<void> => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

// ============================================
// EVENT HANDLERS
// ============================================

const toggleChat = (): void => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    unreadCount.value = 0
    scrollToBottom()
  }
}

const closeChat = (): void => {
  isOpen.value = false
}



const handleKeyPress = (event: KeyboardEvent): void => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
}

// ============================================
// WATCHERS
// ============================================

watch(messages, () => {
  scrollToBottom()
}, { deep: true })
</script>

<template>
  <!-- ============================================ -->
  <!-- FLOATING CHAT BUTTON -->
  <!-- ============================================ -->
  <div class="fixed bottom-6 right-6 z-50 sm:right-6">
    <!-- Chat Toggle Button -->
    <button
      @click="toggleChat"
      class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-out focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-opacity-50"
      :class="{ 'rotate-0': !isOpen, 'rotate-90': isOpen }"
      aria-label="Toggle chat"
    >
      <!-- Chat Icon (when closed) -->
      <svg
        v-if="!isOpen"
        class="w-6 h-6 text-white"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
        />
      </svg>

      <!-- Close Icon (when open) -->
      <svg
        v-else
        class="w-6 h-6 text-white"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>

      <!-- Notification Badge -->
      <span
        v-if="unreadCount > 0 && !isOpen"
        class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>
  </div>

  <!-- ============================================ -->
  <!-- CHATBOX CONTAINER -->
  <!-- ============================================ -->
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-4 scale-95"
  >
    <div
      v-if="isOpen"
      class="fixed bottom-24 right-6 z-40 w-[320px] max-w-[calc(100vw-24px)] sm:max-w-[calc(100vw-48px)] md:max-w-[320px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 left-6 right-6 md:left-auto"
    >
      <!-- ============================================ -->
      <!-- CHAT HEADER -->
      <!-- ============================================ -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <!-- Support Avatar -->
            <div class="relative">
              <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <!-- Online Status Indicator -->
              <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></span>
            </div>

            <!-- Title & Subtitle -->
            <div>
              <h3 class="text-white font-semibold text-lg">Support Chat</h3>
              <p class="text-blue-100 text-sm">We reply instantly</p>
            </div>
          </div>

          <!-- Close Button -->
          <button
            @click="closeChat"
            class="p-1.5 hover:bg-white/20 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white/50"
            aria-label="Close chat"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ============================================ -->
      <!-- MESSAGES AREA -->
      <!-- ============================================ -->
      <div
        ref="messagesContainer"
        class="h-80 overflow-y-auto p-4 space-y-4 bg-gray-50"
      >
        <!-- Message Bubbles -->
        <div
          v-for="message in messages"
          :key="message.id"
          class="flex"
          :class="message.sender === 'user' ? 'justify-end' : 'justify-start'"
        >
          <div
            class="max-w-[75%]"
            :class="message.sender === 'user' ? 'order-2' : 'order-1'"
          >
            <!-- Message Bubble -->
            <div
              class="px-4 py-2.5 rounded-2xl shadow-sm"
              :class="
                message.sender === 'user'
                  ? 'bg-blue-500 text-white rounded-br-md'
                  : 'bg-white text-gray-800 rounded-bl-md border border-gray-200'
              "
            >
              <p class="text-sm leading-relaxed">{{ message.text }}</p>
            </div>

            <!-- Timestamp -->
            <p
              class="text-xs mt-1 px-1"
              :class="
                message.sender === 'user'
                  ? 'text-right text-gray-500'
                  : 'text-left text-gray-400'
              "
            >
              {{ formatTime(message.timestamp) }}
            </p>
          </div>
        </div>

        <!-- Typing Indicator -->
        <Transition
          enter-active-class="transition-opacity duration-200"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-200"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="isTyping" class="flex justify-start">
            <div class="bg-white px-4 py-3 rounded-2xl rounded-bl-md shadow-sm border border-gray-200">
              <div class="flex space-x-1.5">
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
              </div>
            </div>
          </div>
        </Transition>
      </div>

      <!-- ============================================ -->
      <!-- INPUT AREA -->
      <!-- ============================================ -->
      <div class="p-4 bg-white border-t border-gray-100">
        <div class="flex items-center space-x-3">
          <!-- Text Input -->
          <div class="flex-1 relative">
            <input
              v-model="newMessage"
              @keypress="handleKeyPress"
              type="text"
              placeholder="Type your message..."
              class="w-full px-4 py-2.5 bg-gray-100 rounded-full text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200"
            />
          </div>

          <!-- Send Button -->
          <button
            @click="sendMessage"
            :disabled="!newMessage.trim()"
            class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 active:scale-95"
            aria-label="Send message"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
/* ============================================ */
/* CUSTOM ANIMATIONS */
/* ============================================ */

/* Bounce animation for typing indicator */
@keyframes bounce {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-4px);
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite;
}

/* Pulse animation for notification badge */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth scrollbar for messages area */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
