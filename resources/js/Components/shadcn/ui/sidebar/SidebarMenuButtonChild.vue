<script setup>
import { cn } from '@/lib/utils'
import { Primitive } from 'radix-vue'
import { sidebarMenuButtonVariants } from '.'

const props = defineProps({
  as: { type: String, default: 'button' },
  variant: { type: String, default: 'default' },
  size: { type: String, default: 'default' },
  isActive: { type: Boolean, default: false },
  class: { type: null, required: false },
  asChild: { type: Boolean, default: false }
});
</script>

<template>
  <Primitive
    data-sidebar="menu-button"
    :as-child="props.asChild"
    :data-size="props.size"
    :data-active="props.isActive"
    :class="cn(sidebarMenuButtonVariants({ variant: props.variant, size: props.size }), props.class)"
    :as="props.as"
    v-bind="$attrs"
  >
    <slot />
  </Primitive>
</template>
