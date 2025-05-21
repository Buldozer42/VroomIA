import { createSlice, PayloadAction } from "@reduxjs/toolkit";

type Appointment = {
  operations: string[];
  startDate: Date;
  endDate: Date;
  comments: string;
  price: number;
};

type AppointmentState = Appointment[];

const initialState: AppointmentState = [
  {
    operations: ["Vidange"],
    startDate: new Date("2025-06-12"),
    endDate: new Date("2025-06-17"),
    comments: "Veuillez-vous munir de votre carte grise et les clés du véhicule",
    price: 99,
  },
];

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
