// resources/assets/js/components/ChatMessages.vue

<template>
  <div class="chat-history" ref="messages">
    <ul class="m-b-0">
      <li class="clearfix" v-for="message in messages" :key="message.id">
        <div
          class="message-data"
          :class="message.user_id == user.id ? 'text-right' : ''"
        >
          <span class="message-data-time">{{
            formatDateToNormal(message.created_at)
          }}</span>
          <img
            v-if="message.user_id == user.id"
            src="https://bootdey.com/img/Content/avatar/avatar7.png"
            alt="avatar"
          />
          <!-- <img
            src="partner.avatar_url"
            alt="avatar"
          /> -->
        </div>
        <div
          class="message"
          :class="
            message.user_id == user.id
              ? 'other-message float-right'
              : 'my-message'
          "
        >
          {{ message.message }}
        </div>
      </li>
    </ul>

    <div class="mx-5 d-flex typing-box" v-if="isTyping">
      <div id="wave">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
      <div class="name">{{ user.name }} is typing</div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["messages", "user", "partner", "isTyping"],

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
