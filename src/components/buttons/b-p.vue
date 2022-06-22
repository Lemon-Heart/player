<template>
  <button>
    <span v-if="props.isPlay">❚❚</span>
    <svg v-else width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.67 10.92c.82.49.82 1.67 0 2.16l-8.79 5.17c-.83.5-1.88-.1-1.88-1.08V6.83c0-.97 1.05-1.57 1.88-1.08l8.8 5.17Z" fill="currentColor"></path>
    </svg>
  </button>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({
  isPlay: {
    type: Boolean,
    required: true
  }
})
</script>