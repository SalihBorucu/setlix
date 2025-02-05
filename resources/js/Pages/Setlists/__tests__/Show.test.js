import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import { nextTick } from 'vue';
import Show from '../Show.vue';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';

// Mock Inertia
const mockInertia = {
    get: vi.fn(),
    delete: vi.fn()
};

vi.mock('@inertiajs/vue3', () => ({
    Link: {
        name: 'Link',
        template: '<a><slot /></a>'
    },
    router: mockInertia
}));

describe('Setlists/Show', () => {
    const mockBand = {
        id: 1,
        name: 'Test Band'
    };

    const mockSetlist = {
        id: 1,
        name: 'Test Setlist',
        description: 'Test Description',
        total_duration: 720,
        songs: [
            {
                id: 1,
                name: 'Song 1',
                duration_seconds: 180,
                pivot: {
                    order: 0,
                    notes: 'Note for song 1'
                }
            },
            {
                id: 2,
                name: 'Song 2',
                duration_seconds: 240,
                pivot: {
                    order: 1,
                    notes: 'Note for song 2'
                }
            },
            {
                id: 3,
                name: 'Song 3',
                duration_seconds: 300,
                pivot: {
                    order: 2,
                    notes: 'Note for song 3'
                }
            }
        ]
    };

    const mountComponent = (props = {}) => {
        return mount(Show, {
            props: {
                band: mockBand,
                setlist: mockSetlist,
                isAdmin: true,
                ...props
            },
            global: {
                plugins: [createTestingPinia()],
                stubs: {
                    'Link': true,
                    'Head': true,
                    'AuthenticatedLayout': {
                        template: '<div><slot /><slot name="header" /></div>'
                    }
                }
            }
        });
    };

    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders the component with setlist details', () => {
        const wrapper = mountComponent();
        
        expect(wrapper.text()).toContain('Test Setlist');
        expect(wrapper.text()).toContain('Test Description');
        expect(wrapper.text()).toContain('12:00'); // 720 seconds formatted
    });

    it('shows all songs in correct order', () => {
        const wrapper = mountComponent();
        const songElements = wrapper.findAll('[data-test="song-item"]');
        
        expect(songElements).toHaveLength(3);
        mockSetlist.songs.forEach((song, index) => {
            expect(songElements[index].text()).toContain(song.name);
            expect(songElements[index].text()).toContain(song.pivot.notes);
        });
    });

    it('shows edit button for admin users', () => {
        const wrapper = mountComponent({ isAdmin: true });
        const editButton = wrapper.find('[data-test="edit-setlist"]');
        
        expect(editButton.exists()).toBe(true);
    });

    it('hides edit button for non-admin users', () => {
        const wrapper = mountComponent({ isAdmin: false });
        const editButton = wrapper.find('[data-test="edit-setlist"]');
        
        expect(editButton.exists()).toBe(false);
    });

    it('shows delete button for admin users', () => {
        const wrapper = mountComponent({ isAdmin: true });
        const deleteButton = wrapper.find('[data-test="delete-setlist"]');
        
        expect(deleteButton.exists()).toBe(true);
    });

    it('hides delete button for non-admin users', () => {
        const wrapper = mountComponent({ isAdmin: false });
        const deleteButton = wrapper.find('[data-test="delete-setlist"]');
        
        expect(deleteButton.exists()).toBe(false);
    });

    it('opens delete confirmation modal when delete is clicked', async () => {
        const wrapper = mountComponent();
        const deleteButton = wrapper.find('[data-test="delete-setlist"]');
        
        await deleteButton.trigger('click');
        
        const modal = wrapper.findComponent(DeleteConfirmationModal);
        expect(modal.props('show')).toBe(true);
    });

    it('deletes setlist when confirmation is confirmed', async () => {
        const wrapper = mountComponent();
        const deleteButton = wrapper.find('[data-test="delete-setlist"]');
        
        await deleteButton.trigger('click');
        
        const modal = wrapper.findComponent(DeleteConfirmationModal);
        await modal.vm.$emit('confirmed');
        
        expect(mockInertia.delete).toHaveBeenCalledWith(
            expect.stringContaining(`/bands/${mockBand.id}/setlists/${mockSetlist.id}`)
        );
    });

    it('toggles performance mode', async () => {
        const wrapper = mountComponent();
        const toggleButton = wrapper.find('[data-test="toggle-performance-mode"]');
        
        expect(wrapper.classes()).not.toContain('performance-mode');
        
        await toggleButton.trigger('click');
        
        expect(wrapper.classes()).toContain('performance-mode');
    });

    it('shows song notes in performance mode', async () => {
        const wrapper = mountComponent();
        const toggleButton = wrapper.find('[data-test="toggle-performance-mode"]');
        
        await toggleButton.trigger('click');
        
        const noteElements = wrapper.findAll('[data-test="song-note"]');
        mockSetlist.songs.forEach((song, index) => {
            expect(noteElements[index].text()).toContain(song.pivot.notes);
            expect(noteElements[index].classes()).toContain('text-lg');
        });
    });

    it('formats duration correctly', () => {
        const wrapper = mountComponent({
            setlist: {
                ...mockSetlist,
                total_duration: 3665 // 1h 1m 5s
            }
        });
        
        expect(wrapper.text()).toContain('61:05');
    });
}); 