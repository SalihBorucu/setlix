import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import { nextTick } from 'vue';
import Index from '../Index.vue';
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

describe('Setlists/Index', () => {
    const mockBand = {
        id: 1,
        name: 'Test Band'
    };

    const mockSetlists = [
        {
            id: 1,
            name: 'First Setlist',
            description: 'Test Description',
            total_duration: 600,
            songs: [
                { id: 1, name: 'Song 1', duration_seconds: 300 },
                { id: 2, name: 'Song 2', duration_seconds: 300 }
            ]
        }
    ];

    const mountComponent = (props = {}) => {
        return mount(Index, {
            props: {
                band: mockBand,
                setlists: mockSetlists,
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

    it('renders the component with setlists', () => {
        const wrapper = mountComponent();
        
        expect(wrapper.text()).toContain('Test Band');
        expect(wrapper.text()).toContain('First Setlist');
        expect(wrapper.text()).toContain('10:00'); // 600 seconds formatted
    });

    it('shows create button for admin users', () => {
        const wrapper = mountComponent({ isAdmin: true });
        const createButton = wrapper.find('[data-test="create-setlist"]');
        
        expect(createButton.exists()).toBe(true);
    });

    it('hides create button for non-admin users', () => {
        const wrapper = mountComponent({ isAdmin: false });
        const createButton = wrapper.find('[data-test="create-setlist"]');
        
        expect(createButton.exists()).toBe(false);
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
            expect.stringContaining(`/bands/${mockBand.id}/setlists/${mockSetlists[0].id}`)
        );
    });

    it('formats duration correctly', () => {
        const wrapper = mountComponent({
            setlists: [{
                ...mockSetlists[0],
                total_duration: 3665 // 1h 1m 5s
            }]
        });
        
        expect(wrapper.text()).toContain('61:05');
    });

    it('shows empty state when no setlists', () => {
        const wrapper = mountComponent({ setlists: [] });
        
        expect(wrapper.text()).toContain('No setlists found');
    });
}); 