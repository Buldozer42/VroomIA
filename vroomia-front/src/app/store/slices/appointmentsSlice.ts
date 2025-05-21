// store/slices/appointmentSlice.ts
import { createSlice, PayloadAction } from '@reduxjs/toolkit';

interface AppointmentState {
  voitureId: string;
  concessionnaire: string;
  operations: string[];
  date: string;
  userInfo: {
    nom: string;
    prenom: string;
    email: string;
    adresse: string;
  };
}

const initialState: AppointmentState = {
  voitureId: '',
  concessionnaire: '',
  operations: [],
  date: '',
  userInfo: {
    nom: '',
    prenom: '',
    email: '',
    adresse: '',
  },
};

const appointmentSlice = createSlice({
  name: 'appointment',
  initialState,
  reducers: {
    setVoitureId: (state, action: PayloadAction<string>) => {
      state.voitureId = action.payload;
    },
    setConcessionnaire: (state, action: PayloadAction<string>) => {
      state.concessionnaire = action.payload;
    },
    addOperation: (state, action: PayloadAction<string>) => {
      state.operations.push(action.payload);
    },
    setDate: (state, action: PayloadAction<string>) => {
      state.date = action.payload;
    },
    setUserInfo: (
      state,
      action: PayloadAction<Partial<AppointmentState['userInfo']>>
    ) => {
      state.userInfo = { ...state.userInfo, ...action.payload };
    },
    resetAppointment: () => initialState,
  },
});

export const {
  setVoitureId,
  setConcessionnaire,
  addOperation,
  setDate,
  setUserInfo,
  resetAppointment,
} = appointmentSlice.actions;

export default appointmentSlice.reducer;