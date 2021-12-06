// resources/assets/js/components/ChatBody.vue

<template>
  <div class="container">
    <div class="row clearfix">
      <div class="col-lg-12">
        <div class="card chat-app d-flex flex-row">
          <div id="plist" class="people-list col-lg-3 col-md-4">
            <!-- <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fa fa-search"></i
                ></span>
              </div>
              <input type="text" class="form-control" placeholder="Search..." />
            </div> -->
            <search-field v-on:onchange="searchForUser"></search-field>

            <chat-users
              v-on:selectuser="selectCurrUser"
              :searchTerm="searchTerm"
            ></chat-users>
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
      roomData: {},
      searchTerm: ""
    };
  },

  computed: {
    getSelectedUser() {
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
    searchForUser(val) {
      this.searchTerm = val;
    },

    calculateLastSeenTime(date) {
      const nowDate = this.moment(Date.now());
      return this.moment.duration(nowDate.diff(date)).asSeconds();
    },

    connectToChat(roomId) {
      const channel = window.Echo.private(`chat.${roomId}`);

      channel
        .listen("MessageSent", async (e) => {
          // e.message.seen_at = Date.now();
          this.messages.push(e.message);
          await this.checkMessages(this.roomId);
        })
        .listen("MessageDelete", (e) => {
          const deletedMessage = this.messages.find(
            (m) => m.id == e.message.id
          );
          this.$set(deletedMessage, "is_removed", true);
        })
        .listen("BlockUnblockUser", (e) => {
          this.roomData = e;
        })
        // TODO: IF THE OTHE SIDE CHECK THE MESSAGE CHECK SHOW THAT THE MESSAGE IS CHECKED
        .listen("CheckMessages", async (e) => {
          // this.messages = [
          //   ...e.messages,
          //   ...this.messages.filter((msg) => !!msg.seen_at)
          // ].sort((first, second) =>
          //   this.moment(first.created_at).diff(second.created_at)
          // );
          await this.fetchMessages(this.roomId);
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
      await this.fetchMessages(this.roomId);

      // If the user open the chat room check the messages.
      await this.checkMessages(this.roomId);

      this.fetchRoom(this.roomId);
      this.connectToChat(this.roomId);
    },

    async fetchRoom(roomId) {
      await axios.get(`chat/room/data/${roomId}`).then((response) => {
        this.roomData = response.data;
      });
    },

    async fetchRoomId() {
      await axios.get(`chat/room/${this.friendId}`).then((response) => {
        this.roomId = response.data;
      });
    },

    async checkMessages(roomId) {
      await axios
        .put(`/chat/${roomId}/check-messages/${this.friendId}`)
        .then(async (response) => {
          await this.fetchMessages(this.roomId);
        })
        .catch((err) => {
          console.log(err);
        });
    },

    async fetchMessages(roomId) {
      await axios
        .get(`chat/${roomId}/messages`)
        .then((response) => (this.messages = response.data));
    },

    async addMessage(message) {
      this.isTyping = false;
      const messageIndex = this.messages.push(message);
      await axios
        .post(`chat/${this.roomId}/messages`, message)
        .then((response) =>
          this.$set(this.messages, messageIndex - 1, response.data)
        );
    }
  }
};
</script>
