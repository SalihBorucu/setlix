<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSAlertModal } from '@/Components/UI'
import AddMemberModal from '@/Components/Bands/AddMemberModal.vue'
import { ref } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    members: {
        type: Array,
        required: true
    },
    pendingInvitations: {
        type: Array,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
})

const showInviteModal = ref(false)
const deleteModal = ref(null)
const cancelInvitationModal = ref(null)
const memberToRemove = ref(null)
const invitationToCancel = ref(null)

const confirmRemoveMember = (member) => {
    memberToRemove.value = member
    deleteModal.value.open()
}

const handleRemoveMember = () => {
    if (memberToRemove.value) {
        router.delete(route('bands.members.destroy', [props.band.id, memberToRemove.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                memberToRemove.value = null
            }
        })
    }
}

const confirmCancelInvitation = (invitation) => {
    invitationToCancel.value = invitation
    cancelInvitationModal.value.open()
}

const handleCancelInvitation = () => {
    if (invitationToCancel.value) {
        router.delete(route('bands.invitations.destroy', [props.band.id, invitationToCancel.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                invitationToCancel.value = null
            }
        })
    }
}
</script>

<template>
    <Head :title="`Members - ${band.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center">
                        <Link 
                            :href="route('bands.show', band.id)"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700"
                        >
                            {{ band.name }}
                        </Link>
                        <svg class="mx-2 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Band Members
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ members.length }} members
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <DSButton 
                        v-if="isAdmin"
                        variant="primary"
                        @click="showInviteModal = true"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Member
                    </DSButton>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Current Members -->
            <DSCard>
                <div class="px-4 py-5 sm:px-6 border-b border-neutral-200">
                    <h3 class="text-lg font-medium leading-6 text-neutral-900">Current Members</h3>
                </div>
                <ul role="list" class="divide-y divide-neutral-200">
                    <li v-for="member in members" :key="member.id" class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <img 
                                    :src="member.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}`" 
                                    :alt="member.name"
                                    class="h-10 w-10 rounded-full"
                                />
                                <div class="ml-4 min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 truncate">{{ member.name }}</p>
                                    <p class="text-sm text-neutral-500 truncate">{{ member.email }}</p>
                                </div>
                                <span 
                                    :class="[
                                        member.role === 'admin' ? 'bg-primary-100 text-primary-800' : 'bg-neutral-100 text-neutral-800',
                                        'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
                                    ]"
                                >
                                    {{ member.role === 'admin' ? 'Admin' : 'Member' }}
                                </span>
                            </div>
                            <div v-if="isAdmin && member.id !== $page.props.auth.user.id" class="ml-4">
                                <DSButton
                                    variant="danger"
                                    size="sm"
                                    @click="confirmRemoveMember(member)"
                                >
                                    Remove
                                </DSButton>
                            </div>
                        </div>
                    </li>
                </ul>
            </DSCard>

            <!-- Pending Invitations -->
            <DSCard v-if="isAdmin">
                <div class="px-4 py-5 sm:px-6 border-b border-neutral-200">
                    <h3 class="text-lg font-medium leading-6 text-neutral-900">Pending Invitations</h3>
                </div>
                <div v-if="pendingInvitations.length === 0" class="px-4 py-8 text-center text-sm text-neutral-500">
                    No pending invitations
                </div>
                <ul v-else role="list" class="divide-y divide-neutral-200">
                    <li v-for="invitation in pendingInvitations" :key="invitation.id" class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 truncate">{{ invitation.email }}</p>
                                    <p class="text-sm text-neutral-500 truncate">Invited {{ invitation.created_at }}</p>
                                </div>
                                <span 
                                    :class="[
                                        invitation.role === 'admin' ? 'bg-primary-100 text-primary-800' : 'bg-neutral-100 text-neutral-800',
                                        'ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
                                    ]"
                                >
                                    {{ invitation.role === 'admin' ? 'Admin' : 'Member' }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <DSButton
                                    variant="danger"
                                    size="sm"
                                    @click="confirmCancelInvitation(invitation)"
                                >
                                    Cancel
                                </DSButton>
                            </div>
                        </div>
                    </li>
                </ul>
            </DSCard>
        </div>

        <!-- Add Member Modal -->
        <AddMemberModal
            :show="showInviteModal"
            :band-id="band.id"
            @close="showInviteModal = false"
        />

        <!-- Remove Member Modal -->
        <DSAlertModal
            ref="deleteModal"
            :title="`Remove ${memberToRemove?.name || 'Member'}`"
            :message="`Are you sure you want to remove ${memberToRemove?.name || 'this member'} from the band?`"
            type="error"
            confirm-text="Remove"
            :show-cancel="true"
            @confirm="handleRemoveMember"
        />

        <!-- Cancel Invitation Modal -->
        <DSAlertModal
            ref="cancelInvitationModal"
            title="Cancel Invitation"
            message="Are you sure you want to cancel this invitation?"
            type="error"
            confirm-text="Cancel Invitation"
            :show-cancel="true"
            @confirm="handleCancelInvitation"
        />
    </AuthenticatedLayout>
</template> 