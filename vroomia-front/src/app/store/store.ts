// store/store.ts
import { configureStore } from '@reduxjs/toolkit';
import appointmentReducer from './slices/appointmentsSlice';

export const store = configureStore({
  reducer: {
    appointment: appointmentReducer,
  },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;