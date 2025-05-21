// store/store.ts
import { configureStore } from '@reduxjs/toolkit';
import appointmentReducer from './slices/appointmentsSlice';
import vehiclesReducer from './slices/vehiclesSlice';
import garageReducer from './slices/garageSlice';
import operationsReducer from './slices/operationsSlice';

export const store = configureStore({
  reducer: {
    appointment: appointmentReducer,
    vehicules: vehiclesReducer,
    garage: garageReducer,
    operations: operationsReducer
  },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;