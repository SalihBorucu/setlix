# Setlist Breaks & Transitions Implementation

## Overview
Add support for non-song items (breaks/sections) in setlists and transitions between songs. This enhances setlist management by allowing bands to:
- Add breaks with notes (e.g., merch announcements)
- Specify transitions between songs
- Track total setlist duration including breaks
- View breaks and transitions in performance mode

## Database Changes

### New Table: `setlist_items`
```sql
CREATE TABLE setlist_items (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    setlist_id bigint unsigned NOT NULL,
    type ENUM('song', 'break') NOT NULL,
    song_id bigint unsigned NULL,
    title varchar(255) NULL,
    duration_seconds int unsigned NULL,
    notes text NULL,
    order int unsigned NOT NULL,
    created_at timestamp NULL,
    updated_at timestamp NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (setlist_id) REFERENCES setlists(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
);
```

### Update Table: `setlist_song`
Add new column:
```sql
ALTER TABLE setlist_song
ADD COLUMN transition_type ENUM('direct', 'fade', 'stop') NULL;
```

## Model Changes

### SetlistItem Model
```php
class SetlistItem extends Model
{
    protected $fillable = [
        'setlist_id',
        'type',
        'song_id',
        'title',
        'duration_seconds',
        'notes',
        'order'
    ];

    protected $casts = [
        'type' => SetlistItemType::class, // Enum
        'duration_seconds' => 'integer',
        'order' => 'integer'
    ];

    public function setlist()
    {
        return $this->belongsTo(Setlist::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
```

### Setlist Model Updates
```php
class Setlist extends Model
{
    // Add relationship
    public function items()
    {
        return $this->hasMany(SetlistItem::class)->orderBy('order');
    }

    // Update duration calculation
    public function calculateTotalDuration(): int
    {
        return $this->items->sum('duration_seconds');
    }
}
```

## Frontend Implementation

### UI Components Needed

1. Break Creation Modal
```vue
- Duration input (minutes/seconds)
- Title field (optional)
- Notes/script textarea
- Save/Cancel buttons
```

2. Transition Selector
```vue
- Icon-based selector for transition types
- Appears between songs in the setlist
- Visual indicators for each transition type
```

3. Break Display Component
```vue
- Distinct visual style from songs
- Duration display
- Collapsible notes section
- Edit/Delete actions
```

### Visual Indicators for Transitions

```
→  Direct (no break, immediate transition)
⟿  Fade (gradual transition)
∥  Stop (complete stop before next song)
   (empty) Normal break between songs
```

### Performance Mode Updates

1. Break Display
- Clear visual distinction from songs
- Prominent display of break duration
- Easy-to-read notes/script
- Optional countdown timer

2. Transition Indicators
- Clear symbols showing how to transition to next song
- Color coding for different transition types
- Optional automatic timing for transitions

## Implementation Steps

1. Database Updates
- Create migration for setlist_items
- Update setlist_song table
- Run migrations

2. Backend Implementation
- Create SetlistItem model
- Update Setlist model relationships
- Update controllers to handle breaks
- Update duration calculations
- Add validation rules

3. Frontend Updates
- Add break creation UI
- Implement transition selector
- Update drag-and-drop to handle breaks
- Style break items differently
- Update performance mode

4. Testing
- Unit tests for models
- Feature tests for break management
- Frontend component tests
- End-to-end testing

## API Endpoints

### Breaks
```
POST   /api/setlists/{setlist}/breaks       // Create break
PUT    /api/setlists/{setlist}/breaks/{id}  // Update break
DELETE /api/setlists/{setlist}/breaks/{id}  // Delete break
```

### Transitions
```
PUT /api/setlists/{setlist}/transitions     // Update transitions
```

## Future Enhancements

1. Break Templates
- Save common breaks for reuse
- Band-specific break templates
- Quick-add functionality

2. Advanced Transitions
- Custom transition durations
- More transition types
- Transition notes

3. Analytics
- Break duration analytics
- Transition statistics
- Set timing optimization

4. Performance Mode
- Visual countdown for breaks
- Automatic progression
- Break notifications

## Migration Strategy

1. Initial Release
- Basic break functionality
- Simple transitions
- Essential UI components

2. Phase 2
- Enhanced UI/UX
- Templates
- Advanced transitions

3. Phase 3
- Analytics
- Advanced performance mode features
- Mobile optimizations

## Notes

- Keep breaks simple initially
- Focus on user experience
- Ensure clear visual distinction between songs and breaks
- Make transition selection intuitive
- Consider mobile users in design
- Plan for future expandability 