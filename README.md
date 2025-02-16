# 🎸 AI Setlist Manager

A modern web application for bands to manage their setlists, songs, and performances. Built with Laravel, Vue.js, and love for music.

## ✨ Features

### 🎵 Song Management
- Create and manage your band's song library
- Track song duration, notes, and attachments
- Upload and manage song documents (PDF/TXT)
- Add external links to resources

### 📋 Setlist Creation
- Create dynamic setlists with drag-and-drop interface
- Automatic duration calculations
- Performance mode for live shows
- Real-time updates and collaboration
- Add performance notes to songs

### 👥 Band Management
- Create and manage multiple bands
- Role-based access control (Admin/Member)
- Collaborative setlist management
- Member invitation system

## 🚀 Tech Stack

### Backend
- PHP 8.2
- Laravel 11
- MySQL
- Docker

### Frontend
- Vue.js 3
- Inertia.js
- Tailwind CSS
- Vite

## 🛠 Installation

### Prerequisites
- Docker
- Node.js (v18+)
- npm or yarn
- Composer

### Setup Steps

1. Clone the repository
```bash
git clone <repository-url>
cd setlix
```

2. Install PHP dependencies
```bash
docker compose exec app composer install
```

3. Install JavaScript dependencies
```bash
npm install
```

4. Environment setup
```bash
cp .env.example .env
docker compose exec app php artisan key:generate
```

5. Database setup
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

6. Start the development server
```bash
# Terminal 1: Start Docker services
docker compose up

# Terminal 2: Start Vite server
npm run dev
```

7. Access the application
```
http://localhost:5174
```

## 🧪 Testing

Run the test suite:

```bash
# PHP tests
docker compose exec app php artisan test

# JavaScript tests
npm run test
```

## 👤 Default Test Account

```
Email: test@example.com
Password: password
```

## 📱 Usage Examples

### Creating a Setlist
1. Log in to your account
2. Navigate to your band's dashboard
3. Click "Create New Setlist"
4. Drag and drop songs to arrange your setlist
5. Add performance notes as needed
6. Save and share with your band

### Performance Mode
1. Open your setlist
2. Click "Performance Mode"
3. Enjoy distraction-free, easy-to-read display
4. Swipe/click to navigate between songs

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
```bash
git checkout -b feature/amazing-feature
```
3. Commit your changes
```bash
git commit -m 'Add some amazing feature'
```
4. Push to the branch
```bash
git push origin feature/amazing-feature
```
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Tailwind CSS](https://tailwindcss.com)
- [Inertia.js](https://inertiajs.com)

## 📞 Support

For support, email support@example.com or create an issue in the repository.

---
Made with ❤️ for musicians
