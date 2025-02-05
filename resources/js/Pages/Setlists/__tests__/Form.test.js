import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import { nextTick } from 'vue';
import Create from '../Create.vue';
import Edit from '../Edit.vue';
import draggable from 'vuedraggable';

// Mock Inertia
const mockInertia = {
    post: vi.fn(),
    patch: vi.fn()
};

vi.mock('@inertiajs/vue3', () => ({
    Link: {
        name: 'Link',
        template: '<a><slot /></a>'
    },
    router: mockInertia,
    useForm: () => ({
        data: vi.fn(),
        post: mockInertia.post,
        patch: mockInertia.patch,
        processing: false,
        errors: {}
    })
}));

describe('Setlists Form Components', () => {
    const mockBand = {
        id: 1,
        name: 'Test Band'
    };

    const mockSongs = [
        { id: 1, name: 'Song 1', duration_seconds: 180 },
        { id: 2, name: 'Song 2', duration_seconds: 240 },
        { id: 3, name: 'Song 3', duration_seconds: 300 }
    ];

    const mockSetlist = {
        id: 1,
        name: 'Test Setlist',
        description: 'Test Description',
        total_duration: 720,
        songs: [mockSongs[0], mockSongs[1]]
    };

    const mountCreate = (props = {}) => {
        return mount(Create, {
            props: {
                band: mockBand,
                availableSongs: mockSongs,
                ...props
            },
            global: {
                plugins: [createTestingPinia()],
                stubs: {
                    'Link': true,
                    'Head': true,
                    'AuthenticatedLayout': {
                        template: '<div><slot /><slot name="header" /></div>'
                    },
                    draggable: true
                }
            }
        });
    };

    const mountEdit = (props = {}) => {
        return mount(Edit, {
            props: {
                band: mockBand,
                setlist: mockSetlist,
                availableSongs: mockSongs.filter(s => !mockSetlist.songs.find(ms => ms.id === s.id)),
                ...props
            },
            global: {
                plugins: [createTestingPinia()],
                stubs: {
                    'Link': true,
                    'Head': true,
                    'AuthenticatedLayout': {
                        template: '<div><slot /><slot name="header" /></div>'
                    },
                    draggable: true
                }
            }
        });
    };

    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('Create Component', () => {
        it('renders the create form', () => {
            const wrapper = mountCreate();
            
            expect(wrapper.find('input[name="name"]').exists()).toBe(true);
            expect(wrapper.find('textarea[name="description"]').exists()).toBe(true);
            expect(wrapper.findComponent(draggable).exists()).toBe(true);
        });

        it('shows available songs', () => {
            const wrapper = mountCreate();
            
            mockSongs.forEach(song => {
                expect(wrapper.text()).toContain(song.name);
            });
        });

        it('updates total duration when songs are added', async () => {
            const wrapper = mountCreate();
            const draggableComponent = wrapper.findComponent(draggable);
            
            await draggableComponent.vm.$emit('change', {
                added: { element: mockSongs[0] }
            });
            
            expect(wrapper.text()).toContain('3:00'); // 180 seconds
        });

        it('submits form with correct data', async () => {
            const wrapper = mountCreate();
            
            await wrapper.find('input[name="name"]').setValue('New Setlist');
            await wrapper.find('textarea[name="description"]').setValue('New Description');
            
            const form = wrapper.find('form');
            await form.trigger('submit');
            
            expect(mockInertia.post).toHaveBeenCalledWith(
                expect.stringContaining(`/bands/${mockBand.id}/setlists`),
                expect.objectContaining({
                    name: 'New Setlist',
                    description: 'New Description'
                })
            );
        });
    });

    describe('Edit Component', () => {
        it('renders the edit form with existing data', () => {
            const wrapper = mountEdit();
            
            expect(wrapper.find('input[name="name"]').element.value).toBe(mockSetlist.name);
            expect(wrapper.find('textarea[name="description"]').element.value).toBe(mockSetlist.description);
        });

        it('shows selected songs in correct order', () => {
            const wrapper = mountEdit();
            
            const songElements = wrapper.findAll('.selected-songs .song-item');
            expect(songElements).toHaveLength(mockSetlist.songs.length);
            
            mockSetlist.songs.forEach((song, index) => {
                expect(songElements[index].text()).toContain(song.name);
            });
        });

        it('updates total duration when songs are reordered', async () => {
            const wrapper = mountEdit();
            const draggableComponent = wrapper.findComponent(draggable);
            
            await draggableComponent.vm.$emit('change', {
                moved: {
                    element: mockSetlist.songs[0],
                    newIndex: 1,
                    oldIndex: 0
                }
            });
            
            expect(wrapper.text()).toContain('12:00'); // 720 seconds
        });

        it('submits form with updated data', async () => {
            const wrapper = mountEdit();
            
            await wrapper.find('input[name="name"]').setValue('Updated Setlist');
            await wrapper.find('textarea[name="description"]').setValue('Updated Description');
            
            const form = wrapper.find('form');
            await form.trigger('submit');
            
            expect(mockInertia.patch).toHaveBeenCalledWith(
                expect.stringContaining(`/bands/${mockBand.id}/setlists/${mockSetlist.id}`),
                expect.objectContaining({
                    name: 'Updated Setlist',
                    description: 'Updated Description'
                })
            );
        });
    });
}); 