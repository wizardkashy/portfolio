`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 05/31/2024 01:34:34 PM
// Design Name: 
// Module Name: data_path
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module data_path #(
    parameter PC_W = 8,
    parameter INS_W = 32,
    parameter RF_ADDRESS = 5,
    parameter DATA_W = 32,
    parameter DM_ADDRESS = 9,
    parameter ALU_CC_W = 4
    )
    (
    input clk,
    input reset,
    input reg_write,
    input mem2reg,
    input alu_src,
    input mem_write,
    input mem_read,
    input [ALU_CC_W-1:0] alu_cc,
    output [6:0] opcode,
    output [6:0] funct7,
    output [2:0] funct3,
    output [DATA_W-1:0] alu_result
    );
    // Program Counter
    wire [PC_W-1:0] PC, PCPlus4;
    assign PCPlus4 = PC + 4;
    FlipFlop ProgramCounter(clk, reset, PCPlus4, PC);
    
    // Instruction Memory
    wire [INS_W-1:0] Instruction;
    InstMem ReadInstruction(PC, Instruction);
    assign opcode = Instruction[6:0];
    assign funct3 = Instruction[14:12];
    assign funct7 = Instruction[31:25];
    
    // Register File
    wire [RF_ADDRESS-1:0] RdAddr1;
    wire [RF_ADDRESS-1:0] RdAddr2;
    wire [RF_ADDRESS-1:0] WrtAddr;
    wire [DATA_W-1:0] Reg1;
    wire [DATA_W-1:0] Reg2;
    wire [DATA_W-1:0] WriteBack_Data;
    assign RdAddr1 = Instruction[19:15];
    assign RdAddr2 = Instruction[24:20];
    assign WrtAddr = Instruction[11:7];
    
    RegFile RegisterFile(
        .clk(clk),
        .reset(reset),
        .rg_wrt_en(reg_write),
        .rg_rd_addr1(RdAddr1),
        .rg_rd_addr2(RdAddr2),
        .rg_wrt_addr(WrtAddr),
        .rg_wrt_data(WriteBack_Data),
        .rg_rd_data1(Reg1),
        .rg_rd_data2(Reg2)
    );
    
    // ImmGenerator
    wire [DATA_W-1:0] ExtImm;
    ImmGen ImmediateGenerator(Instruction, ExtImm);
    
    // ALUSrc MUX
    wire [DATA_W-1:0] SrcB;
    wire Carry_Out, Zero, Overflow;
    MUX srcb(ExtImm, Reg2, alu_src, SrcB);
    
    // ALU
    ALU_32 ALU(
    .A_in(Reg1),
    .B_in(SrcB),
    .ALU_Sel(alu_cc),
    .ALU_Out(alu_result),
    .Carry_Out(Carry_Out),
    .Zero(Zero),
    .Overflow(Overflow)
    );
    
    // data_mem
    wire [DATA_W-1:0] DataMem_read;
    wire [DM_ADDRESS-1:0] DM_Address;
    assign DM_Address = alu_result[8:0];
    DataMem DataMemory(mem_read, mem_write, DM_Address, Reg2, DataMem_read);
    
    // WriteBack MUX
    MUX writeback(DataMem_read, alu_result, mem2reg, WriteBack_Data);
    
endmodule
