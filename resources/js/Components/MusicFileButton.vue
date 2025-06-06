<script setup>
import visitExternalLink from "@/Utilities/visitExternalLink.js";
import GuitarIcon from '@/Components/Icons/Instruments/guitar.svg';
import CelloIcon from '@/Components/Icons/Instruments/cello.svg';
import SaxophoneIcon from '@/Components/Icons/Instruments/saxophone.svg';
import TrumpetIcon from '@/Components/Icons/Instruments/trumpet.svg';
import TromboneIcon from '@/Components/Icons/Instruments/trombone.svg';
import KeyboardIcon from '@/Components/Icons/Instruments/keyboard.svg';
import ViolinIcon from '@/Components/Icons/Instruments/violin.svg';
import DrumsIcon from '@/Components/Icons/Instruments/drums.svg';
import PercussionIcon from '@/Components/Icons/Instruments/percussion.svg';
import MicrophoneIcon from '@/Components/Icons/Instruments/microphone.svg';
import FluteIcon from '@/Components/Icons/Instruments/flute.svg';
import NotesIcon from '@/Components/Icons/Instruments/notes.svg';

const props = defineProps({
    file: {
        type: Object,
        required: true
    },
    song: {
        type: Object,
        required: true
    },
    band: {
        type: Object,
        required: true
    },
    classes: {
        type: String,
        default: 'h-5 w-auto text-neutral-700 hover:text-neutral-800'
    }
});

const handleFileDownload = (file, song) => {
    let url = route('songs.files.download', [props.band.id, song.id, file.id])

    if (file.original_filename.endsWith('.musicxml')) {
        url = route('songs.music-xml-viewer', [file.id]);
    }

    visitExternalLink(
        url,
        true,
        true
    );
};
</script>

<template>
    <button
        class="flex items-center"
        @click="handleFileDownload(file, song)"
    >
        <GuitarIcon :class="classes" v-if="file.instrument === 'guitar'" />
        <CelloIcon :class="classes" v-else-if="file.instrument === 'cello'" />
        <SaxophoneIcon :class="classes" v-else-if="file.instrument === 'saxophone'" />
        <TrumpetIcon :class="classes" v-else-if="file.instrument === 'trumpet'" />
        <TromboneIcon :class="classes" v-else-if="file.instrument === 'trombone'" />
        <KeyboardIcon :class="classes" v-else-if="file.instrument === 'keyboard'" />
        <ViolinIcon :class="classes" v-else-if="file.instrument === 'violin'" />
        <DrumsIcon :class="classes" v-else-if="file.instrument === 'drums'" />
        <PercussionIcon :class="classes" v-else-if="file.instrument === 'percussion'" />
        <MicrophoneIcon :class="classes" v-else-if="file.instrument === 'microphone'" />
        <FluteIcon :class="classes" v-else-if="file.instrument === 'flute'" />
        <NotesIcon :class="classes" v-else />

        <slot />
    </button>
</template>
