<template>
  <a :href="link" class="card-hover block bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
    <div class="p-6">
      <div class="flex flex-col items-center text-center">
        <div :class="['icon-container mb-5 p-5 rounded-2xl border', bgGradient, borderColor]">
          <div class="relative">
            <div :class="['w-20 h-20 rounded-full flex items-center justify-center', bgColor]">
              <i :class="[icon, 'text-white text-3xl']"></i>
            </div>
            <div v-if="badgeIcon" class="absolute -top-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center" :class="badgeColor">
              <i :class="[badgeIcon, 'text-white text-xs']"></i>
            </div>
          </div>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ title }}</h3>
        <p class="text-gray-600 mb-4">{{ description }}</p>
        <div class="flex items-center font-medium" :class="textColor">
          <span>{{ footerText }}</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </div>
    <div :class="['px-6 py-3 border-t', footerBg, borderFooter]">
      <div class="flex justify-between text-sm" :class="textFooter">
        <slot name="footer"></slot>
      </div>
    </div>
  </a>
</template>

<script setup>
defineProps({
  title: String,
  description: String,
  icon: String,
  badgeIcon: String,
  badgeColor: String,
  bgGradient: String,
  borderColor: String,
  bgColor: String,
  footerText: String,
  link: String,
  footerBg: String,
  borderFooter: String,
  textFooter: String,
  textColor: String,
});
</script>

<style scoped>
.card-hover {
  transition: all 0.3s ease;
  transform: translateY(0);
}
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
              0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.icon-container {
  transition: all 0.3s ease;
}
.card-hover:hover .icon-container {
  transform: scale(1.05);
}
</style>
