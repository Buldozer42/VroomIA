import { createSlice, PayloadAction } from "@reduxjs/toolkit";

interface Appointment {
  operations: string[];
  startDate: Date;
  endDate: Date;
  comments: string;
  price: number;
};

const initialState : Appointment[] = [];

const appointmentSlice = createSlice({
  name: "appointment",
  initialState,
  reducers: {
    addAppointment: (state, action: PayloadAction<Appointment>) => {
      state.push(action.payload);
    },
    resetAppointments: () => initialState,
  },
});

export const { addAppointment, resetAppointments } = appointmentSlice.actions;

export default appointmentSlice.reducer;
