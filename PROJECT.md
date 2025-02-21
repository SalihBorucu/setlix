# Setlix Documentation

## Tech Stack 🛠

### Backend
- PHP/Laravel 11
- MySQL Database
- Docker-based setup (not using Laravel Sail)
- PHPUnit for testing

### Frontend
- Vue.js with Inertia.js
- Tailwind CSS
- Vue Test Utils for component testing
- Vite for development

### Rules
 - Keep the design consistent
 - Keep the code clean and readable
 - Keep the code DRY
 - App is in production, so we need to be careful with the code
 - We need to be able to add new features without breaking the app
 - We need to be able to fix bugs without breaking the app
 - We need to be able to add new pages without breaking the app
 - We need to be able to add new components without breaking the app
 - We need to be able to add new routes without breaking the app
 

## Core Features 🎸

### User & Band Management
- Multi-user system with authentication
- Band creation and membership
- Role-based permissions (admin/member)
- Test credentials: test@example.com

### Song Management
- Full CRUD operations
- Properties:
  - Name
  - Duration (stored in seconds)
  - Notes
  - Optional URL
  - Document attachments (PDF/TXT, max 10MB)
- Real-time duration calculations

### Setlist Management
- Drag-and-drop song arrangement
- Automatic duration calculations
- Performance mode for live shows
- Song ordering with notes
- Real-time updates

## Implementation Progress 📈

### Completed Features ✅
1. Database Structure
   - All migrations created and tested
   - Models with relationships
   - Soft deletes implemented

2. Authentication & Authorization
   - User authentication system
   - Role-based permissions (admin/member)
   - Policy-based authorization

3. Band Management
   - CRUD operations
   - Member management
   - Admin assignment
   - Band dashboard

4. Song Management
   - CRUD operations
   - File uploads
   - Duration calculations
   - Document handling

5. Setlist Core
   - Basic CRUD operations
   - Song ordering
   - Duration calculations
   - Relationship with bands and songs

6. Testing
   - Model tests
   - Controller tests
   - Policy tests
   - Vue component tests for Index pages

7. Frontend Components
   - Band management views
   - Song management views
   - Basic setlist views
   - Drag-and-drop implementation

8. Data Seeding
   - User seeder
   - Band seeder
   - Song seeder
   - Setlist seeder

### Next Steps 🎯

1. High Priority
   - [ ] Implement WebSocket for real-time updates
   - [ ] Add performance mode UI
   - [ ] Implement offline mode with local storage
   - [ ] Add error handling and recovery
   - [ ] Implement auto-save functionality

2. UI/UX Improvements
   - [ ] Add loading states
   - [ ] Improve responsive design
   - [ ] Add animations for drag-and-drop
   - [ ] Implement dark mode
   - [ ] Add keyboard shortcuts

3. Feature Enhancements
   - [ ] Add setlist templates
   - [ ] Implement setlist sharing
   - [ ] Add band chat feature
   - [ ] Create PDF export for setlists
   - [ ] Add statistics dashboard

4. Testing & Documentation
   - [ ] Add E2E tests
   - [ ] Improve test coverage
   - [ ] Add API documentation
   - [ ] Create user guide
   - [ ] Add JSDoc comments

5. Performance Optimization
   - [ ] Implement caching
   - [ ] Optimize database queries
   - [ ] Add lazy loading
   - [ ] Implement pagination
   - [ ] Optimize asset loading

6. Security Enhancements
   - [ ] Add rate limiting
   - [ ] Implement 2FA
   - [ ] Add session management
   - [ ] Improve file upload security
   - [ ] Add audit logging

## Database Structure 📊

### Models & Relationships

#### User
```php
- belongsToMany Band
- Attributes: name, email, password
- Role management through pivot
```

#### Band
```php
- belongsToMany User
- hasMany Song
- hasMany Setlist
- Soft deletes enabled
- Attributes: name, description
```

#### Song
```php
- belongsTo Band
- belongsToMany Setlist
- Soft deletes enabled
- Attributes: name, duration_seconds, notes, url, document_path
```

#### Setlist
```php
- belongsTo Band
- belongsToMany Song
- Soft deletes enabled
- Attributes: name, description, total_duration, song_order
```

## Testing Structure 🧪

### Backend Tests

#### Model Tests
- `BandTest`: Relationships and model methods
- `SongTest`: Duration formatting, document handling
- `SetlistTest`: Song ordering, duration calculations

#### Controller Tests
- `BandControllerTest`: CRUD and authorization
- `SongControllerTest`: File uploads and management
- `SetlistControllerTest`: Song ordering and updates

#### Policy Tests
- `BandPolicyTest`: Role-based authorization rules

### Frontend Tests

#### Component Tests
- Index components: List rendering and filtering
- Create/Edit forms: Validation and submission
- Show components: Data display and interactions

## Authorization System 🔒

### Admin Capabilities
- Create/edit/delete bands
- Manage songs and setlists
- Add/remove band members
- Upload and manage documents

### Member Capabilities
- View band content
- Access performance mode
- View songs and setlists
- No modification rights

## Development Setup 💻

### Environment Setup
```bash
# Backend (Docker)
docker compose exec app bash

# Frontend (Local)
npm install
npm run dev
```

### Key URLs
- Frontend Dev Server: http://localhost:5174
- API: http://localhost

### File Structure
```
resources/js/Pages/
├── Bands/
│   ├── Create.vue
│   ├── Edit.vue
│   ├── Index.vue
│   └── Show.vue
├── Songs/
│   ├── Create.vue
│   ├── Edit.vue
│   ├── Index.vue
│   └── Show.vue
└── Setlists/
    ├── Create.vue
    ├── Edit.vue
    ├── Index.vue
    └── Show.vue
```

## Implementation Details 🔍

### Duration Handling
- Storage: Seconds in database
- Display: MM:SS format in frontend
- Automatic total calculations for setlists

### File Management
- Storage: Public disk
- Automatic cleanup on deletion
- Validation:
  - Size: Max 10MB
  - Types: PDF, TXT
  - Secure file handling

### Real-time Features
- Vue.js reactivity system
- Automatic duration updates
- Drag-and-drop using vuedraggable
- Optimistic UI updates

## Data Seeding 🌱

### Default Data
- Test user + 4 random users
- 5 predefined bands
- 5-10 songs per band
- 2-4 setlists per band

### Seeding Command
```bash
php artisan migrate:fresh --seed
```

## Development Notes 📝

### Best Practices
- Use soft deletes for data recovery
- Implement proper authorization
- Add comprehensive tests
- Keep components small and focused
- Use TypeScript for better type safety

### Common Issues
- Docker permissions
- File upload configurations
- Duration calculation precision
- Real-time update conflicts

## Future Enhancements 🚀
- Offline mode support
- WebSocket integration
- Mobile app version
- Enhanced performance mode
- Band chat/collaboration features

## Contributing 🤝
1. Fork the repository
2. Create feature branch
3. Write tests
4. Submit pull request

## License 📄
MIT License 

# AI Setlist Manager Project Log

## Recent Changes

### Design System Implementation
- Created a unified design system with components:
  - DSButton: Flexible button component with variants (primary, secondary, outline, ghost, danger)
  - DSCard: Consistent card styling
  - DSInput: Enhanced form input handling
  - DSAlert: System feedback component
  - DSForm: Form wrapper with consistent styling

### UI/UX Improvements
- Enhanced mobile responsiveness across all pages
- Improved button sizing and spacing for mobile devices
- Added breadcrumb navigation for better wayfinding
- Implemented consistent spacing and typography
- Added loading states for better user feedback

### Setlist Management
- Enhanced setlist creation and editing interface
- Improved drag-and-drop functionality:
  - Fixed issues with song reordering
  - Added proper handling of song notes
  - Implemented smooth transitions between available and selected songs
- Added performance notes for individual songs in setlists
- Improved duration calculations and display
- Enhanced error handling and validation

### Song Management
- Added formatted timestamps for song creation dates
- Improved song document handling
- Enhanced song duration input and formatting
- Added proper error states for form inputs

### Band Management
- Improved band member management interface
- Enhanced band settings and permissions
- Added cover image upload functionality
- Improved band statistics display

## Upcoming Features
- Performance mode for live shows
- Song categorization and tagging
- Advanced setlist analytics
- Band member communication tools
- Integration with external music services

## Technical Improvements
- Implemented proper error handling
- Enhanced form validation
- Improved state management
- Added proper TypeScript types
- Enhanced API response handling

## Design Improvements
- Consistent color scheme using CSS custom properties
- Enhanced dark mode support
- Improved accessibility
- Better mobile responsiveness
- Enhanced loading states and transitions

## Known Issues
- None at present

## Next Steps
- Implement real-time collaboration features
- Add advanced search and filtering
- Enhance performance mode features
- Add export/import functionality
- Implement band member messaging 