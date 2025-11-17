<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
        <div v-if="flashMessage" 
     :class="[
       'p-3 mt-3 rounded text-white transition-opacity duration-300',
       flashType === 'success' ? 'bg-green-500' : 'bg-red-500'
     ]">
      {{ flashMessage }}
    </div>
          <div class="p-3 mt-3 ">
            <p><strong>My Wallet Balance:</strong> <span id="user-balance">{{ formatCurrency(balance) }}</span></p>

            <!-- Action Buttons -->
            <div class="mt-4 space-x-4">
              <button @click="showAddForm = !showAddForm; showSendForm = false" class="px-4 py-2 bg-blue-600 text-white rounded">
                Add Money
              </button>

              <button                
                @click="showSendForm = !showSendForm; showAddForm = false"
                class="px-4 py-2 bg-green-600 text-white rounded"
              >
                Send Money
              </button>
            </div>

            <!-- Add Money Form -->
            <div v-if="showAddForm" class="add-money-form mt-6">
              <h3 class="font-semibold mb-2">Add Money</h3>
              <form @submit.prevent="addFunds">
                <input
                  v-model.number="addAmount"
                  type="number"
                  step="0.01"
                  placeholder="Enter amount"
                  required
                  class="border p-2 rounded mr-2"
                />
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                  Add
                </button>
              </form>
            </div>

            <!-- Transfer Form -->
            <div v-if="showSendForm" class="transfer-form mt-6">
              <h3 class="font-semibold mb-2">Send Money</h3>
              <form @submit.prevent="transfer">
                <input
                  v-model.number="receiver_id"
                  type="number"
                  placeholder="Receiver ID"
                  required
                  class="border p-2 rounded mr-2"
                />
                <input
                  v-model.number="amount"
                  type="number"
                  step="0.01"
                  placeholder="Amount"
                  required
                  class="border p-2 rounded mr-2"
                />
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">
                  Send
                </button>
              </form>
            </div>

            <!-- Transaction History -->
            <div class="mt-8">
              <h3 class="font-semibold mb-2">Transaction History</h3>
              
<ul v-if="transactions.length > 0">
  <li v-for="tx in transactions" :key="tx.id">
    <span v-if="tx.sender.id === user.id">
      Sent to {{ tx.receiver.name }}: {{ formatCurrency(tx.amount) }}
      (Commission: {{ formatCurrency(tx.commission_fee) }})
    </span>
    <span v-else>
      Received from {{ tx.sender.name }}:  {{ formatCurrency(tx.amount) }}
    </span>
  </li>
</ul>
  <!-- If no transactions -->
  <p v-else class="text-gray-500 italic">
    No transactions found yet.
  </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

axios.defaults.withCredentials = true;

// ------------------ STATE ------------------
const balance = ref(0);
const transactions = ref([]);
const user = ref({ id: null, name: '' });

const receiver_id = ref('');
const amount = ref('');
const addAmount = ref('');
const flashMessage = ref('');
const flashType = ref('');

// Form toggles
const showAddForm = ref(false);
const showSendForm = ref(false);

// ------------------ FLASH MESSAGE ------------------
const showFlash = (message, type = 'success') => {
  flashMessage.value = message;
  flashType.value = type;
  setTimeout(() => {
    flashMessage.value = '';
  }, 3000);
};

// ------------------ FETCH USER & TRANSACTIONS ------------------
const fetchUser = async () => {
  try {
    const res = await axios.get('/api/user');
    user.value = res.data;    
  } catch (err) {
    console.error('Failed to fetch user', err);
  }
};

const fetchTransactions = async () => {
  try {
    const res = await axios.get('/api/transactions');
    balance.value = res.data.balance;
    transactions.value = res.data.transactions;
  } catch (err) {
    console.error(err);
  }
};

// ------------------ FORMAT CURRENCY ------------------
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
};

// ------------------ ADD FUNDS ------------------
const addFunds = async () => {
  if (!addAmount.value || addAmount.value <= 0) {
    showFlash('Please enter a valid amount.', 'error');
    return;
  }

  try {
    const res = await axios.post('/api/add-funds', { amount: addAmount.value });
    showFlash(res.data.message, 'success');
    balance.value = res.data.balance;
    addAmount.value = '';
    showAddForm.value = false;
  } catch (err) {
    showFlash(err.response?.data?.error || 'Failed to add funds', 'error');
  }
};

// ------------------ TRANSFER MONEY ------------------
const transfer = async () => {
  if (receiver_id.value === user.value.id) {
    showFlash("You cannot send money to yourself.", "error");
    return;
  }
  if (!receiver_id.value || !amount.value || amount.value <= 0) {
    showFlash('Please enter valid receiver ID and amount.', 'error');
    return;
  }

  try {
    const res = await axios.post('/api/transactions', {
      receiver_id: receiver_id.value,
      amount: amount.value,
    });

    receiver_id.value = '';
    amount.value = '';
    showFlash('Fund Transferred Successfully', 'success');
    fetchTransactions();
    showSendForm.value = false;
  } catch (err) {
    const message =
      err.response?.data?.message ||
      err.response?.data?.error ||
      'Transaction failed. Please try again.';
    showFlash(message, 'error');
  }
};

// ------------------ INIT ECHO ------------------

const initEcho = (userId) => {   
  if (!userId) return;
  const echo =  window.Echo;
echo.private(`user.${user.value.id}`)
    .subscribed(() => {
        console.log("CONNECTED to channel user." + user.value.id);
    })
    .error((err) => {
        console.error("Channel error:", err);
    })
    .listen('.balance.updated', (e) => {
        console.log("EVENT RECEIVED:", e);
        balance.value = e.newBalance;       
        if (e.transaction) {               
        transactions.value.unshift(e.transaction);
    }
    });
};


// ------------------ MOUNT ------------------
onMounted(async () => {
  await fetchUser();
  await fetchTransactions();
});

// Watch for user.id and initialize Echo once available
watch(() => user.value?.id,(id) => {  
    if (id) initEcho(id);
  },{ immediate: true }
);
</script>

<style scoped>
button {
  cursor: pointer;
}
</style>
