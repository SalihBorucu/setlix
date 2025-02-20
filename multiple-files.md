# Multiple Files Implementation Plan

## Overview
Implement multiple file support for songs with different types (lyrics, notes, chords, tabs, etc.), using S3 storage and making files easily accessible in performance mode.

## Phase 1: Database Changes

### Create song_files table
```sql
CREATE TABLE song_files (
    id bigint PRIMARY KEY,
    song_id bigint REFERENCES songs(id),
    type varchar(255),
    file_path varchar(255),
    original_filename varchar(255),
    file_size bigint,
    created_at timestamp,
    updated_at timestamp
);
```

### Model Updates
- Create SongFile model with relationships
- Update Song model with hasMany relationship to SongFiles
- Remove old document-related fields from songs table

## Phase 2: S3 Integration

### Configuration
- Add S3 credentials to .env:
  ```
  AWS_ACCESS_KEY_ID=
  AWS_SECRET_ACCESS_KEY=
  AWS_DEFAULT_REGION=
  AWS_BUCKET=
  AWS_URL=
  ```
- Configure filesystem.php for S3
- Create dedicated disk for song files

### File Service
Create SongFileService to handle:
- File uploads to S3
- Unique filename generation
- File deletions
- Type validation
- Size limits (10MB per file)
- Total size limits per song

## Phase 3: Controller Updates

### SongController Changes
- Modify store/update methods for multiple files
- Add file management endpoints:
  - Download file
  - Delete file
  - Add new file to existing song
  - Update file type

### Request Validation
Create new requests:
- StoreSongFileRequest
- UpdateSongFileRequest
Validation rules:
- Max 10 files per song
- 10MB per file
- Allowed file types (PDF, TXT)
- Required fields: type, file

## Phase 4: Frontend Changes

### Create.vue Updates
- Add dynamic file upload sections:
  ```html
  <div v-for="(fileGroup, index) in fileGroups" :key="index">
    <select v-model="fileGroup.type">
      <option value="lyrics">Lyrics</option>
      <option value="notes">Notes</option>
      <option value="chords">Chords</option>
      <option value="tabs">Tabs</option>
      <!-- Expandable for future types -->
    </select>
    <input type="file" @change="handleFileUpload($event, index)">
    <button @click="removeFileGroup(index)">Remove</button>
  </div>
  <button @click="addNewFileGroup" :disabled="fileGroups.length >= 10">
    Add Another File
  </button>
  ```

### Edit.vue Updates
- Display existing files by type
- Allow adding new files (up to 10 total)
- Delete existing files
- Change file types
- Preview functionality

### Show.vue Updates
- Group files by type
- Download buttons
- Preview functionality
- File management interface

## Phase 5: Performance Mode Updates

### Setlist/Show.vue Performance Mode
- Add file type sections
- Quick access tabs for different file types
- File preview functionality
- Mobile-optimized viewer
- Offline access capability

### UI Components
- File type icons
- Preview modal
- Download progress
- Error handling
- Loading states

## Phase 6: Cleanup & Migration

### Migration Script
```php
public function up()
{
    // Create new table
    // Move existing documents
    // Update references
    // Remove old columns
}

public function down()
{
    // Revert changes
    // Restore old structure
}
```

### Test Updates
- Multiple file upload tests
- S3 integration tests
- File type validation tests
- Size limit tests
- Performance mode file access tests

## Implementation Notes

### File Types
- Store as string for flexibility
- Common initial types:
  - lyrics
  - notes
  - chords
  - tabs
  - sheet_music
  - other

### Size Limits
- Individual file: 10MB
- Total per song: 50MB
- Validate both client and server side

### Security
- Validate file types
- Scan for malware
- Secure S3 access
- Proper permission checks

### Performance
- Lazy load files in lists
- Cache file metadata
- Optimize S3 access
- Consider CDN for frequent access

## Future Considerations
- Additional file types
- File versioning
- Collaborative editing
- Mobile app support
- Offline synchronization
- File format conversion 