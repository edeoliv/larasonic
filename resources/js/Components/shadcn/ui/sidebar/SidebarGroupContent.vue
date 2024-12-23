<script setup>
import { cn } from '@/lib/utils'
import { Primitive } from 'radix-vue'

const props = defineProps({
  as: { type: String, default: undefined },
  asChild: { type: Boolean, default: false },
  class: { type: null, required: false }
})
</script>

<template>
  <div
    data-sidebar="group-content"
    :class="cn('w-full text-sm', props.class)"
  >
    <slot />
  </div>
</template>
