# Laravel AI Agent - E-commerce Assistant

An AI-powered conversational e-commerce assistant built with Laravel 12, Laravel AI SDK, Inertia.js, and Vue 3.

## Overview

This project provides a **conversational chat interface** where users can interact with an AI agent to:
- Browse products and categories
- View their order history
- Create new orders
- Cancel existing orders

All through natural language - no manual UI navigation required!

---

## Architecture

### Tech Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **AI Framework**: Laravel AI SDK (v0.4.3) with Prism
- **AI Providers**: Gemini, Groq, OpenAI (configurable)
- **Frontend**: Inertia.js + Vue 3 + Tailwind CSS
- **Database**: MySQL

### How It Works

```
User Message (Vue Chat)
        ↓
POST /ai-agent (requires authentication)
        ↓
AiAgentController::callAgent()
        ↓
AiAssistant::make(user)->prompt(message)
        ↓
Laravel AI SDK → AI Provider (Gemini/Groq)
        ↓
AI decides which tool(s) to call based on user intent
        ↓
Tool executes database operations
        ↓
Result returned to AI → AI formats response
        ↓
Response sent back to chat UI
```

---

## Features

### AI Tools (Functions the AI Can Call)

| Tool | Description | Parameters |
|------|-------------|-------------|
| `ListProductTool` | List all products or filter by category | `category` (optional) |
| `ListCategoryTool` | List all product categories | none |
| `ListOrderToolForUser` | Show the current user's order history | none |
| `CreateOrderTool` | Create a new order for a product | `product_name`, `quantity` |
| `CancelOrderTool` | Cancel an existing order by invoice ID | `invoice_id` |

### Example Conversations

**User:** "What products do you have?"
- AI calls `ListProductTool` → Returns product list

**User:** "Show my orders"
- AI calls `ListOrderToolForUser` → Returns user's order history

**User:** "I want to buy a Laptop"
- AI calls `CreateOrderTool(product_name: "Laptop", quantity: 1)` → Creates order

**User:** "Cancel order INV-ABC123"
- AI calls `CancelOrderTool(invoice_id: "INV-ABC123")` → Cancels order

---

## Project Structure

```
Source/
├── app/
│   ├── Ai/
│   │   ├── Agents/
│   │   │   └── AiAssistant.php      # Main AI agent definition
│   │   └── Tools/
│   │       ├── ListProductTool.php  # List products
│   │       ├── ListCategoryTool.php # List categories
│   │       ├── ListOrderToolForUser.php # List user's orders
│   │       ├── CreateOrderTool.php  # Create new order
│   │       └── CancelOrderTool.php  # Cancel order
│   ├── Http/
│   │   └── Controllers/
│   │       └── AiAgentController.php # Handles /ai-agent route
│   └── Models/
│       ├── Order.php    # Order model with relationships
│       ├── Product.php  # Product model
│       ├── Category.php # Category model
│       └── User.php     # User model
├── config/
│   └── ai.php           # AI provider configuration
├── database/
│   ├── migrations/      # Database schema
│   └── seeders/         # Sample data (30 orders, products, etc.)
├── resources/
│   └── js/
│       └── components/
│           └── ChatBox.vue          # Floating chat widget
├── routes/
│   └── web.php         # Route definitions
└── .env                # Environment variables
```

---

## Database Schema

### Orders Table
| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `invoice_id` | string | Unique order number (e.g., INV-ABC123) |
| `user_id` | foreignId | Reference to users |
| `product_id` | foreignId | Reference to products |
| `qty` | unsignedInteger | Quantity ordered |
| `status` | enum | `ordered`, `cancelled`, `completed` |
| `created_at` | timestamp | Order creation time |
| `updated_at` | timestamp | Last update |
| `deleted_at` | timestamp | Soft delete |

### Products Table
| Field | Type |
|-------|------|
| `id` | bigint |
| `category_id` | foreignId |
| `name` | string |
| `slug` | string |
| `price` | decimal(10,2) |
| `description` | text |

### Categories Table
| Field | Type |
|-------|------|
| `id` | bigint |
| `name` | string |
| `slug` | string |

---

## Configuration

### Environment Variables (`.env`)

```env
# AI Providers - At least one required
GEMINI_API_KEY=your_gemini_api_key
GROQ_API_KEY=your_groq_api_key

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

### AI Config (`config/ai.php`)

The default provider can be changed:
```php
'default' => 'groq',  // Change to 'gemini', 'openai', etc.
```

Available providers:
- `gemini` - Google Gemini
- `groq` - Groq (fast inference)
- `openai` - OpenAI GPT
- `anthropic` - Anthropic Claude
- `azure` - Azure OpenAI
- `ollama` - Local Ollama

---

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Docker (optional, for containerized setup)

### Local Setup (Without Docker)

```bash
# 1. Install dependencies
composer install
npm install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate:fresh --seed

# 5. Start the development server
php artisan serve

# 6. In another terminal, start Vite
npm run dev
```

---

## API Endpoints

### POST /ai-agent
Creates an AI agent and processes the user's message.

**Authentication:** Required (middleware: `auth`)

**Request:**
```json
{
  "message": "Show my orders"
}
```

**Response:**
```json
{
  "reply": "Here are your orders:\n1. Order INV-ABC123: Laptop - Qty: 1 - Status: completed\n2. Order INV-DEF456: Mouse - Qty: 2 - Status: ordered"
}
```

**Error Response:**
```json
{
  "reply": "Sorry, something went wrong. Please try again.",
  "error": "Error message here"
}
```

---

## Key Files Explained

### AiAssistant.php
The main AI agent that:
1. Defines system instructions for the AI's behavior
2. Provides access to tools (ListProductTool, CreateOrderTool, etc.)
3. Receives the current authenticated user for context

### AiAgentController.php
The HTTP controller that:
1. Validates incoming message
2. Creates an instance of AiAssistant with the authenticated user
3. Calls `->prompt()` to invoke the AI
4. Returns the response as JSON

### ChatBox.vue
A Vue 3 component providing:
- Floating chat button
- Message history display
- Typing indicator
- Markdown rendering support

---

## Seeding Data

The project includes seeders for:
- **CategorySeeder** - Product categories
- **ProductSeeder** - Sample products
- **OrderSeeder** - 30 sample orders with random data

To reseed:
```bash
php artisan migrate:fresh --seed
```

---

## Troubleshooting

### "Sorry, something went wrong" error
1. Check your API key is valid in `.env`
2. Check `storage/logs/laravel.log` for errors
3. Increase timeout in controller if API is slow

### No response from AI
1. Verify `GEMINI_API_KEY` or `GROQ_API_KEY` is set in `.env`
2. Check internet connection
3. Verify AI provider is working (check provider status page)

### Tools not working
1. Ensure database is migrated: `php artisan migrate`
2. Verify seeders ran: `php artisan db:seed`
3. Check relationships in models

---

# Docker Setup (Inertia + Vue)

## Structure
- `docker/` — All Docker configuration files
- `Source/` — Place your Laravel 12 + Inertia/Vue files here

## Services
| Service        | URL                    |
|----------------|------------------------|
| Laravel App    | http://localhost:8000  |
| Vite Dev Server| http://localhost:5173  |
| phpMyAdmin     | http://localhost:8080   |
| MySQL          | localhost:3306          |

## Getting Started

1. Copy your Laravel app files into the `Source/` folder.

2. Make sure your `vite.config.js` has the correct server config for Docker:
   ```js
   server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        strictPort: false,
        //for windows polling 
        watch: {
            usePolling: true,
            interval: 100,
            ignored: ['**/node_modules/**', '**/vendor/**'],
        },
    },
   ```

3. From the `docker/` directory, run:
   ```bash
   docker-compose up -d --build
   ```

4. The `node` container will automatically run `npm install && npm run dev`

## Database Credentials
- Database: `laravel`
- Root Password: `root`

---

## License

