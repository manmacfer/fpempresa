<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';

const showingDropdown = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);

// Cerrar dropdown al hacer clic fuera
const closeOnClickOutside = (e) => {
  if (!e.target.closest('.notification-dropdown')) {
    showingDropdown.value = false;
  }
};

onMounted(() => {
  loadNotifications();
  document.addEventListener('click', closeOnClickOutside);
  
  // Recargar notificaciones cada 30 segundos
  const interval = setInterval(loadNotifications, 30000);
  
  onUnmounted(() => {
    clearInterval(interval);
    document.removeEventListener('click', closeOnClickOutside);
  });
});

// Cargar notificaciones
const loadNotifications = async () => {
  try {
    const response = await axios.get('/notifications');
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unread_count;
  } catch (error) {
    console.error('Error al cargar notificaciones:', error);
  }
};

// Marcar como leída y navegar
const handleNotificationClick = async (notification) => {
  if (!notification.is_read) {
    try {
      await axios.patch(`/notifications/${notification.id}/read`);
      notification.is_read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
      console.error('Error al marcar notificación:', error);
    }
  }
  
  if (notification.link) {
    showingDropdown.value = false;
    router.visit(notification.link);
  }
};

// Marcar todas como leídas
const markAllAsRead = async () => {
  loading.value = true;
  try {
    await axios.patch('/notifications/read-all');
    notifications.value.forEach(n => n.is_read = true);
    unreadCount.value = 0;
  } catch (error) {
    console.error('Error al marcar todas:', error);
  } finally {
    loading.value = false;
  }
};

// Eliminar notificación
const deleteNotification = async (notificationId, event) => {
  event.stopPropagation();
  try {
    await axios.delete(`/notifications/${notificationId}`);
    notifications.value = notifications.value.filter(n => n.id !== notificationId);
    if (!notifications.value.find(n => n.id === notificationId)?.is_read) {
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    }
  } catch (error) {
    console.error('Error al eliminar notificación:', error);
  }
};

// Icono según tipo de notificación
const getNotificationIcon = (type) => {
  const icons = {
    'matching_created': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'matching_validated': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'new_message': 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
    'new_application': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'default': 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
  };
  return icons[type] || icons['default'];
};

// Color según tipo
const getNotificationColor = (type) => {
  const colors = {
    'matching_created': 'text-green-600 bg-green-50 dark:bg-green-900/20',
    'matching_validated': 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
    'new_message': 'text-purple-600 bg-purple-50 dark:bg-purple-900/20',
    'new_application': 'text-orange-600 bg-orange-50 dark:bg-orange-900/20',
    'default': 'text-gray-600 bg-gray-50 dark:bg-gray-900/20'
  };
  return colors[type] || colors['default'];
};
</script>

<template>
  <div class="notification-dropdown relative">
    <!-- Campanita -->
    <button
      @click="showingDropdown = !showingDropdown"
      class="relative rounded-xl p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-indigo-400"
      aria-label="Notificaciones"
    >
      <!-- Icono de campanita -->
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
        />
      </svg>
      
      <!-- Badge de contador (solo si hay no leídas) -->
      <span
        v-if="unreadCount > 0"
        class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white ring-2 ring-white dark:ring-gray-900"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown de notificaciones -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-show="showingDropdown"
        class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none dark:border-gray-700 dark:bg-gray-800"
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
            Notificaciones
            <span v-if="unreadCount > 0" class="ml-2 text-xs text-gray-500">({{ unreadCount }} nuevas)</span>
          </h3>
          <button
            v-if="unreadCount > 0 && !loading"
            @click="markAllAsRead"
            class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            Marcar todas leídas
          </button>
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
              />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No tienes notificaciones</p>
          </div>

          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="handleNotificationClick(notification)"
            class="group relative cursor-pointer border-b border-gray-100 px-4 py-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
            :class="{ 'bg-indigo-50/50 dark:bg-indigo-900/10': !notification.is_read }"
          >
            <div class="flex gap-3">
              <!-- Icono -->
              <div class="flex-shrink-0">
                <div class="flex h-10 w-10 items-center justify-center rounded-full" :class="getNotificationColor(notification.type)">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNotificationIcon(notification.type)" />
                  </svg>
                </div>
              </div>

              <!-- Contenido -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ notification.title }}
                  </p>
                  <!-- Indicador de no leída -->
                  <div
                    v-if="!notification.is_read"
                    class="flex-shrink-0 h-2 w-2 rounded-full bg-indigo-600"
                  ></div>
                </div>
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                  {{ notification.message }}
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                  {{ notification.time_ago }}
                </p>
              </div>

              <!-- Botón eliminar (aparece al hover) -->
              <button
                @click="deleteNotification(notification.id, $event)"
                class="flex-shrink-0 opacity-0 transition-opacity group-hover:opacity-100"
              >
                <svg class="h-4 w-4 text-gray-400 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
