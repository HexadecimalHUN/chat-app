// resources/assets/js/components/ChatBody.vue

<template>
  <div class="container">
    <div class="row clearfix">
      <div class="col-lg-12">
        <div class="card chat-app d-flex flex-row">
          <div id="plist" class="people-list col-lg-3 col-md-4">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fa fa-search"></i
                ></span>
              </div>
              <input type="text" class="form-control" placeholder="Search..." />
            </div>

            <chat-users v-on:selectuser="selectCurrUser"></chat-users>
          </div>

          <div
            class="chat col-lg-9 col-md-8 d-flex flex-column"
            v-if="!!selectedUser"
          >
            <chat-user-header
              :selectedUser="getSelectedUser"
              :user="user"
              :roomId="roomId"
              :roomData="roomData"
            ></chat-user-header>

            <!-- TODO: Change parner link -->
            <chat-messages
              :messages="messages"
              :isTyping="isTyping"
              :user="user"
              :roomId="roomId"
            ></chat-messages>

            <chat-form
              v-on:messagesent="addMessage"
              :user="user"
              :roomId="roomId"
              :roomData="roomData"
            ></chat-form>
          </div>

          <div
            class="d-flex align-self-center w-100 justify-content-center"
            v-else
          >
            <h2>No chat opened yet!</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],

  data() {
    return {
      friendId: null,
      messages: [],
      isTyping: false,
      selectedUser: null,
      roomId: null,
      roomData: {}
    };
  },

  computed: {
    getSelectedUser() {
      console.log(this.selectedUser);
      if (!this.selectedUser) {
        return {
          name: "",
          last_seen: ""
        };
      } else {
        return this.selectedUser;
      }
    }
  },

  watch: {
    roomId(val, oldVal) {
      if (oldVal) {
        this.disconnectFromChat(oldVal);
        this.connectToChat(val);
      }
    }
  },
  methods: {
    calculateLastSeenTime(date) {
      const nowDate = this.moment(Date.now());
      return this.moment.duration(nowDate.diff(date)).asSeconds();
    },

    connectToChat(roomId) {
      const channel = window.Echo.private(`chat.${roomId}`);

      channel
        .listen("MessageSent", (e) => {
          console.log(e);
          this.messages.push(e.message);
        })
        .listen("MessageDelete", (e) => {
          console.log("message has been deleted!");
          console.log(e);
          const deletedMessage = this.messages.find(
            (m) => m.id == e.message.id
          );
          this.$set(deletedMessage, "is_removed", true);
        })
        .listen("BlockUnblockUser", (e) => {
          this.roomData = e;
        })
        .listenForWhisper("typing", (e) => {
          this.isTyping = true;
          setTimeout(() => {
            this.isTyping = false;
          }, 3000);
        });
    },
    disconnectFromChat(roomId) {
      window.Echo.leave(`chat.${roomId}`);
    },

    async selectCurrUser(userData) {
      this.selectedUser = userData;
      this.friendId = userData.id;

      await this.fetchRoomId();
      this.fetchRoom(this.roomId);
      this.fetchMessages(this.roomId);
      this.connectToChat(this.roomId);
    },

    fetchRoom(roomId) {
      axios.get(`chat/room/data/${roomId}`).then((response) => {
        this.roomData = response.data;
        console.log(response.data);
      });
    },
    async fetchRoomId() {
      await axios.get(`chat/room/${this.friendId}`).then((response) => {
        this.roomId = response.data;
      });
    },

    fetchMessages(roomId) {
      axios
        .get(`chat/${roomId}/messages`)
        .then((response) => (this.messages = response.data));
    },

    addMessage(message) {
      this.isTyping = false;
      this.messages.push(message);
      axios
        .post(`chat/${this.roomId}/messages`, message)
        .then((response) => console.log(response.data));
    }
  }
};
</script>
