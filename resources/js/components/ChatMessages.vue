// resources/assets/js/components/ChatMessages.vue

<template>
  <div class="chat-history" ref="messages">
    <ul class="m-b-0">
      <li class="clearfix" v-for="message in messages" :key="message.id">
        <div
          class="message-data"
          :class="message.user_id == user.id ? 'text-right' : ''"
        >
          <span class="message-data-time">
            {{ formatDateToNormal(message.created_at) }}
          </span>

          <img
            v-if="message.user_id == user.id"
            :src="user.avatar_url"
            alt="avatar"
          />
        </div>

        <div
          class="d-flex message-row"
          :class="
            message.user_id == user.id
              ? 'justify-content-end'
              : 'justify-content-start'
          "
        >
          <message-actions
            v-if="message.user_id == user.id"
            :message="message"
            :roomId="roomId"
          ></message-actions>
          <div
            class="message"
            :class="
              message.user_id == user.id
                ? 'other-message float-right'
                : 'my-message'
            "
          >
            {{ message.is_removed ? "Message is removed" : message.message }}
          </div>
        </div>
      </li>

      <li class="clearfix" v-if="isTyping">
        <div class="message my-message typing-box">
          <div class="d-flex" v-if="isTyping">
            <div id="wave">
              <span class="dot"></span>
              <span class="dot"></span>
              <span class="dot"></span>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: ["messages", "user", "roomId", "isTyping"],

  created() {
    this.updatePosition();
  },
  updated() {
    this.$nextTick(this.updatePosition());
  },
  methods: {
    // TODO: Fix Message possition
    updatePosition() {
      const el = this.$refs.messages;

      if (el) {
        // Use el.scrollIntoView() to instantly scroll to the element
        el.scrollTop = el.scrollHeight;
      }
    },
    formatDateToNormal(date) {
      const momentDate = this.moment(date);
      if (momentDate.isSame(new Date(), "minute")) {
        return "Today, " + momentDate.from(Date.now());
      } else if (momentDate.isSame(new Date(), "day")) {
        return momentDate.format("HH:mm");
      } else {
        return momentDate.format("ddd HH:mm");
      }
    }
  }
};
</script>
